<?php

namespace Tests\Feature\Auth;

use App\Models\AccountRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    // ── Forgot-password form ──────────────────────────────────────────────────

    public function test_forgot_password_page_is_accessible_as_guest(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('Forgot Password');
    }

    public function test_forgot_password_page_redirects_authenticated_users(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('password.request'))
            ->assertRedirect();
    }

    public function test_submitting_unknown_username_still_shows_success(): void
    {
        $this->post(route('password.email'), ['username' => 'nobody'])
            ->assertSessionHas('message');

        $this->assertDatabaseMissing('account_requests', ['type' => 'password_reset']);
    }

    public function test_submitting_inactive_user_shows_success_without_creating_request(): void
    {
        $user = User::factory()->create(['status' => 'pending']);

        $this->post(route('password.email'), ['username' => $user->username])
            ->assertSessionHas('message');

        $this->assertDatabaseMissing('account_requests', ['user_id' => $user->id, 'type' => 'password_reset']);
    }

    public function test_active_user_can_submit_forgot_password_request(): void
    {
        $user = User::factory()->create(['status' => 'active']);

        $this->post(route('password.email'), ['username' => $user->username])
            ->assertSessionHas('message');

        $this->assertDatabaseHas('account_requests', [
            'user_id' => $user->id,
            'type'    => 'password_reset',
            'status'  => 'pending',
        ]);
    }

    public function test_duplicate_pending_request_is_blocked(): void
    {
        $user = User::factory()->create(['status' => 'active']);

        AccountRequest::create([
            'user_id' => $user->id,
            'type'    => 'password_reset',
            'status'  => 'pending',
        ]);

        $this->post(route('password.email'), ['username' => $user->username])
            ->assertSessionHas('message');

        // Should still be exactly one request, not two
        $this->assertDatabaseCount('account_requests', 1);
    }

    public function test_username_field_is_required(): void
    {
        $this->post(route('password.email'), [])
            ->assertSessionHasErrors('username');
    }

    // ── Admin generates reset link ────────────────────────────────────────────

    public function test_admin_can_generate_reset_link(): void
    {
        $admin   = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $user    = User::factory()->create(['status' => 'active']);
        $request = AccountRequest::create([
            'user_id' => $user->id,
            'type'    => 'password_reset',
            'status'  => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.account-requests.generate-reset-link', $request))
            ->assertSessionHas('reset_url');

        $request->refresh();
        $this->assertNotNull($request->reset_token);
        $this->assertNotNull($request->reset_token_expires_at);
        $this->assertTrue($request->reset_token_expires_at->isFuture());
    }

    public function test_generate_reset_link_is_rejected_for_non_pending_request(): void
    {
        $admin   = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $user    = User::factory()->create(['status' => 'active']);
        $request = AccountRequest::create([
            'user_id' => $user->id,
            'type'    => 'password_reset',
            'status'  => 'approved',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.account-requests.generate-reset-link', $request))
            ->assertSessionHas('error');
    }

    public function test_generate_reset_link_is_rejected_for_wrong_type(): void
    {
        $admin   = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $user    = User::factory()->create(['status' => 'active']);
        $request = AccountRequest::create([
            'user_id'      => $user->id,
            'type'         => 'username_change',
            'new_username' => 'newname',
            'status'       => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.account-requests.generate-reset-link', $request))
            ->assertSessionHas('error');
    }

    public function test_generate_link_requires_admin_role(): void
    {
        $editor  = User::factory()->create(['role' => 'editor', 'status' => 'active']);
        $user    = User::factory()->create(['status' => 'active']);
        $request = AccountRequest::create([
            'user_id' => $user->id,
            'type'    => 'password_reset',
            'status'  => 'pending',
        ]);

        $this->actingAs($editor)
            ->post(route('admin.account-requests.generate-reset-link', $request))
            ->assertForbidden();
    }

    // ── Reset password via token ──────────────────────────────────────────────

    public function test_valid_token_shows_reset_form(): void
    {
        $user    = User::factory()->create(['status' => 'active']);
        $token   = Str::random(64);
        AccountRequest::create([
            'user_id'                => $user->id,
            'type'                   => 'password_reset',
            'status'                 => 'pending',
            'reset_token'            => $token,
            'reset_token_expires_at' => now()->addHours(24),
        ]);

        $this->get(route('password.reset', $token))
            ->assertOk()
            ->assertSee('Set New Password');
    }

    public function test_invalid_token_redirects_to_login(): void
    {
        $this->get(route('password.reset', 'badtoken'))
            ->assertRedirect(route('login'));
    }

    public function test_expired_token_redirects_to_login(): void
    {
        $user  = User::factory()->create(['status' => 'active']);
        $token = Str::random(64);
        AccountRequest::create([
            'user_id'                => $user->id,
            'type'                   => 'password_reset',
            'status'                 => 'pending',
            'reset_token'            => $token,
            'reset_token_expires_at' => now()->subHour(),
        ]);

        $this->get(route('password.reset', $token))
            ->assertRedirect(route('login'));
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user  = User::factory()->create(['status' => 'active']);
        $token = Str::random(64);
        AccountRequest::create([
            'user_id'                => $user->id,
            'type'                   => 'password_reset',
            'status'                 => 'pending',
            'reset_token'            => $token,
            'reset_token_expires_at' => now()->addHours(24),
        ]);

        $this->post(route('password.store', $token), [
            'password'              => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertRedirect(route('login'))
          ->assertSessionHas('message');

        // Request should be marked approved and token cleared
        $req = AccountRequest::first();
        $this->assertSame('approved', $req->status);
        $this->assertNull($req->reset_token);
    }

    public function test_password_reset_enforces_complexity_policy(): void
    {
        $user  = User::factory()->create(['status' => 'active']);
        $token = Str::random(64);
        AccountRequest::create([
            'user_id'                => $user->id,
            'type'                   => 'password_reset',
            'status'                 => 'pending',
            'reset_token'            => $token,
            'reset_token_expires_at' => now()->addHours(24),
        ]);

        // 'abc' is too short for even the Basic level (min 6)
        $this->post(route('password.store', $token), [
            'password'              => 'abc',
            'password_confirmation' => 'abc',
        ])->assertSessionHasErrors('password');
    }

    public function test_token_cannot_be_reused_after_successful_reset(): void
    {
        $user  = User::factory()->create(['status' => 'active']);
        $token = Str::random(64);
        AccountRequest::create([
            'user_id'                => $user->id,
            'type'                   => 'password_reset',
            'status'                 => 'pending',
            'reset_token'            => $token,
            'reset_token_expires_at' => now()->addHours(24),
        ]);

        $this->post(route('password.store', $token), [
            'password'              => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        // Second use should redirect to login with invalid link message
        $this->post(route('password.store', $token), [
            'password'              => 'AnotherPass456!',
            'password_confirmation' => 'AnotherPass456!',
        ])->assertRedirect(route('login'));
    }

    // ── Admin reject of password reset request ───────────────────────────────

    public function test_rejecting_password_reset_request_sends_correct_notification(): void
    {
        $admin   = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $user    = User::factory()->create(['status' => 'active']);
        $request = AccountRequest::create([
            'user_id' => $user->id,
            'type'    => 'password_reset',
            'status'  => 'pending',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.account-requests.reject', $request), []);

        $request->refresh();
        $this->assertSame('rejected', $request->status);

        // Notification should reference password reset, not account deletion
        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'title'   => 'Request Not Approved',
        ]);
    }
}
