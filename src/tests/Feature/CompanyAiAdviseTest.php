<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Services\ChatGptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Tests\TestCase;

final class CompanyAiAdviseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // ダミーAPIキー（ChatGptService 内の未設定ガードを回避）
        Config::set('services.openai.key', 'test-key');
        putenv('OPENAI_API_KEY=test-key');
        $_ENV['OPENAI_API_KEY'] = 'test-key';
    }

    private function actingUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    private function makeCompany(): Company
    {
        return Company::factory()->create();
    }

    #[Test]
    public function 生成成功_200_アドバイス本文が返る(): void
    {
        $this->actingUser();
        $company = $this->makeCompany();

        // ChatGptService をコンテナに差し替え：正常応答
        $fake = \Mockery::mock(ChatGptService::class);
        $fake->shouldReceive('adviseForCompany')
            ->once()
            ->andReturn('次の一手：担当者に来週の面談候補日を送る');
        $this->app->instance(ChatGptService::class, $fake);

        $res = $this->postJson("/companies/{$company->id}/ai/advise");

        $res->assertOk()
            ->assertJsonPath('data.advice', '次の一手：担当者に来週の面談候補日を送る');
    }

    #[Test]
    public function OpenAIダウン想定_503_フロント案1で拾えるペイロード形式(): void
    {
        $this->actingUser();
        $company = $this->makeCompany();

        // 503 相当の例外をサービスが投げる想定
        $fake = \Mockery::mock(ChatGptService::class);
        $fake->shouldReceive('adviseForCompany')
            ->once()
            ->andThrow(new ServiceUnavailableHttpException(0, 'OpenAI service unavailable'));
        $this->app->instance(ChatGptService::class, $fake);

        $res = $this->postJson("/companies/{$company->id}/ai/advise");

        // bootstrap/app.php の withExceptions() で 503 + JSON に変換される前提
        $res->assertStatus(503)
            ->assertJson(
                fn($json) =>
                $json->has('code')
                    ->whereType('code', 'string')
                    ->has('message')
                    ->etc() // 余剰キー許容
            );
    }

    #[Test]
    public function レート制限_429_retry_after付与とcode確認(): void
    {
        $this->actingUser();
        $company = $this->makeCompany();

        // サービス側が429を投げる想定（Retry-After=60秒）
        $exception = new TooManyRequestsHttpException(
            60, // Retry-After 秒
            'Rate limited'
        );

        $fake = \Mockery::mock(ChatGptService::class);
        $fake->shouldReceive('adviseForCompany')->once()->andThrow($exception);
        $this->app->instance(ChatGptService::class, $fake);

        $res = $this->postJson("/companies/{$company->id}/ai/advise");

        // bootstrap/app.php の withExceptions() で 429 + JSON に変換される前提
        $res->assertStatus(429)
            ->assertHeader('Retry-After', '60')
            ->assertJson(['code' => 'RATE_LIMITED'])
            ->assertJson(
                fn($json) =>
                $json->where('retry_after', 60)->etc()
            );
    }
}
