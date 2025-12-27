<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_cannot_access_mypage()
    {
        $response = $this->get(route('mypage.index'));

        $response->assertRedirect('/login');
    }

    /** @test */
    public function logged_in_user_can_access_mypage()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('mypage.index'));

        $response->assertStatus(200);
    }
}
