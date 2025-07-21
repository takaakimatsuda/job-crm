<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InteractionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 履歴を登録できる()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $this->actingAs($user)
            ->post("/companies/{$company->id}/interactions", [
                'interaction_date' => now()->format('Y-m-d'),
                'type' => '電話',
                'memo' => '面談メモ',
            ])
            ->assertRedirect(); // back() によるリダイレクトなのでURL特定しない

        $this->assertDatabaseHas('interactions', [
            'company_id' => $company->id,
            'type' => '電話',
            'memo' => '面談メモ',
        ]);
    }

    /** @test */
    public function 履歴を編集できる()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $interaction = $company->interactions()->create([
            'interaction_date' => now()->format('Y-m-d'),
            'type' => '電話',
            'memo' => '初期メモ',
        ]);

        $this->actingAs($user)
            ->put("/interactions/{$interaction->id}", [
                'interaction_date' => now()->subDay()->format('Y-m-d'),
                'type' => '面談',
                'memo' => 'メモを更新しました',
            ])
            ->assertRedirect(); // back()

        $this->assertDatabaseHas('interactions', [
            'id' => $interaction->id,
            'type' => '面談',
            'memo' => 'メモを更新しました',
        ]);
    }

    /** @test */
    public function 履歴を削除できる()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $interaction = $company->interactions()->create([
            'interaction_date' => now()->format('Y-m-d'),
            'type' => 'メール',
            'memo' => '削除対象メモ',
        ]);

        $this->actingAs($user)
            ->delete("/interactions/{$interaction->id}")
            ->assertRedirect(); // back()

        $this->assertDatabaseMissing('interactions', [
            'id' => $interaction->id,
        ]);
    }
}
