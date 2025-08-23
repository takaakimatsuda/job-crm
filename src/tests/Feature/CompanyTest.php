<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function 企業情報を更新できる()
    {
        $user = User::factory()->create(); // ログインユーザー
        $company = Company::factory()->create([
            'name' => 'Old Company',
        ]);

        $response = $this
            ->actingAs($user)
            ->put(route('companies.update', $company), [
                'name' => 'New Company',
                'status' => 'active',
                'hope_level' => 3,
            ]);

        $response->assertRedirect(route('companies.show', $company));
        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'New Company',
        ]);
    }

    #[Test]
    public function 必須項目が抜けていると更新できない()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('companies.edit', $company))
            ->put(route('companies.update', $company), [
                'name' => '', // 必須
            ]);

        $response->assertRedirect(route('companies.edit', $company));
        $response->assertSessionHasErrors(['name']);
    }
}
