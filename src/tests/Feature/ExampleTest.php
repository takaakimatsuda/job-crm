<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ExampleTest extends TestCase
{
    use RefreshDatabase; // ← 追加：テストごとにmigrateしてロールバック

    #[Test]
    public function guest_is_redirected_from_root_to_login(): void
    {
        $this->get('/')
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function authenticated_user_is_redirected_from_root_to_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/')
            ->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function login_page_is_accessible(): void
    {
        $this->get(route('login'))
            ->assertStatus(200);
    }
}
