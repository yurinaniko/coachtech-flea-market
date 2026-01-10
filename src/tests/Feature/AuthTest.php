<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;

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

    public function test_register_name_is_required()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_register_email_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'テスト',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_register_email_must_be_valid()
    {
        $response = $this->post('/register', [
            'name' => 'テスト',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_register_password_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function register_password_less_than_8_characters_is_invalid()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '1234567', // 7文字
            'password_confirmation' => '1234567',
        ]);
        $response->assertSessionHasErrors(['password']);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_register_password_confirmation_must_match()
    {
        $response = $this->post('/register', [
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password456',
        ]);

        $response->assertSessionHasErrors(['password_confirmation']);
    }

    /** @test */
    public function user_can_register_and_redirect_to_profile_create()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'success@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertRedirect(route('verification.notice'));
        $this->assertDatabaseHas('users', [
        'email' => 'success@example.com',
        ]);
        $this->assertAuthenticated();
    }

    public function test_login_email_is_required()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_login_password_is_required()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_login_fails_with_invalid_credentials()
    {
        $response = $this->post('/login', [
            'email' => 'notfound@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $password = 'password123';

        $user = User::factory()->create([
            'password' => bcrypt($password),
        ]);
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);
        $response->assertRedirect(route('mypage.index'));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /** @test */
    public function registered_user_receives_verification_email()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'verify@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'verify@example.com')->first();

        $this->assertNotNull($user);

        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );
    }
    /** @test */
    public function user_can_verify_email_with_valid_verification_link()
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    /** @test */
    public function verified_user_is_redirected_to_profile_create_page()
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect(route('profile.create'));
    }
}