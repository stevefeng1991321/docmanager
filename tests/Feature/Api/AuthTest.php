<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function activeUser(array $attrs = []): User
    {
        return User::factory()->create(array_merge(['role' => 'viewer', 'status' => 'active'], $attrs));
    }

    // ── Login ────────────────────────────────────────────────────────────────

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = $this->activeUser();

        $response = $this->postJson('/api/auth/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'token_type', 'expires_in', 'user']);
    }

    public function test_login_returns_user_data(): void
    {
        $user = $this->activeUser();

        $response = $this->postJson('/api/auth/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertJsonPath('user.username', $user->username)
            ->assertJsonPath('user.role', $user->role);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = $this->activeUser();

        $response = $this->postJson('/api/auth/login', [
            'username' => $user->username,
            'password' => 'wrong-password',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['username']);
    }

    public function test_login_fails_for_unknown_username(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'username' => 'nobody',
            'password' => 'password',
        ]);

        $response->assertUnprocessable();
    }

    public function test_login_fails_for_pending_user(): void
    {
        $user = User::factory()->create(['role' => 'viewer', 'status' => 'pending']);

        $response = $this->postJson('/api/auth/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertForbidden()
            ->assertJsonFragment(['message' => 'Account is pending activation.']);
    }

    public function test_login_fails_for_inactive_user(): void
    {
        $user = User::factory()->create(['role' => 'viewer', 'status' => 'inactive']);

        $response = $this->postJson('/api/auth/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertForbidden()
            ->assertJsonFragment(['message' => 'Account is inactive.']);
    }

    public function test_login_fails_for_locked_account(): void
    {
        $user = $this->activeUser(['locked_until' => Carbon::now()->addMinutes(10)]);

        $response = $this->postJson('/api/auth/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertStatus(423);
    }

    public function test_failed_login_increments_attempt_counter(): void
    {
        $user = $this->activeUser();

        $this->postJson('/api/auth/login', [
            'username' => $user->username,
            'password' => 'wrong',
        ]);

        $this->assertDatabaseHas('users', [
            'id'                    => $user->id,
            'failed_login_attempts' => 1,
        ]);
    }

    public function test_successful_login_resets_lockout_counters(): void
    {
        $user = $this->activeUser(['failed_login_attempts' => 3]);

        $this->postJson('/api/auth/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('users', [
            'id'                    => $user->id,
            'failed_login_attempts' => 0,
        ]);
    }

    public function test_login_validates_required_fields(): void
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['username', 'password']);
    }

    // ── Logout ───────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_logout(): void
    {
        $user  = $this->activeUser();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/auth/logout');

        $response->assertOk()
            ->assertJsonFragment(['message' => 'Logged out successfully.']);
    }

    public function test_logout_deletes_token_from_database(): void
    {
        $user = $this->activeUser();
        $user->createToken('test');

        $this->assertDatabaseCount('personal_access_tokens', 1);

        $token = $user->createToken('test2')->plainTextToken;
        $this->withToken($token)->postJson('/api/auth/logout');

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    // ── Me ───────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_get_profile(): void
    {
        $user = $this->activeUser();

        $response = $this->actingAs($user)->getJson('/api/auth/me');

        $response->assertOk()
            ->assertJsonPath('username', $user->username)
            ->assertJsonPath('name', $user->name);
    }

    public function test_unauthenticated_request_to_me_is_rejected(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertUnauthorized();
    }
}
