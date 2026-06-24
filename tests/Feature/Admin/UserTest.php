<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'status' => 'active']);
    }

    private function editor(): User
    {
        return User::factory()->create(['role' => 'editor', 'status' => 'active']);
    }

    // ── Access control ───────────────────────────────────────────────────────

    public function test_admin_can_view_user_list(): void
    {
        $response = $this->actingAs($this->admin())->get(route('admin.users.index'));

        $response->assertOk();
    }

    public function test_editor_cannot_access_user_management(): void
    {
        $response = $this->actingAs($this->editor())->get(route('admin.users.index'));

        $response->assertForbidden();
    }

    // ── Create / Store ───────────────────────────────────────────────────────

    public function test_admin_can_view_create_user_form(): void
    {
        $response = $this->actingAs($this->admin())->get(route('admin.users.create'));

        $response->assertOk();
    }

    public function test_admin_can_create_user(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.users.store'), [
            'username' => 'newuser',
            'name'     => 'New User',
            'password' => 'secret123',
            'role'     => 'viewer',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['username' => 'newuser', 'role' => 'viewer']);
    }

    public function test_store_rejects_duplicate_username(): void
    {
        User::factory()->create(['username' => 'taken']);

        $response = $this->actingAs($this->admin())->post(route('admin.users.store'), [
            'username' => 'taken',
            'name'     => 'Test',
            'password' => 'secret123',
            'role'     => 'viewer',
        ]);

        $response->assertSessionHasErrors(['username']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.users.store'), []);

        $response->assertSessionHasErrors(['username', 'name', 'password', 'role']);
    }

    public function test_store_rejects_invalid_role(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.users.store'), [
            'username' => 'tester',
            'name'     => 'Tester',
            'password' => 'secret123',
            'role'     => 'superadmin',
        ]);

        $response->assertSessionHasErrors(['role']);
    }

    public function test_store_rejects_username_with_special_chars(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.users.store'), [
            'username' => 'bad user!',
            'name'     => 'Test',
            'password' => 'secret123',
            'role'     => 'viewer',
        ]);

        $response->assertSessionHasErrors(['username']);
    }

    // ── Edit / Update ────────────────────────────────────────────────────────

    public function test_admin_can_view_edit_user_form(): void
    {
        $user = User::factory()->create(['role' => 'viewer', 'status' => 'active']);

        $response = $this->actingAs($this->admin())->get(route('admin.users.edit', $user));

        $response->assertOk();
    }

    public function test_admin_can_update_user(): void
    {
        $user = User::factory()->create(['role' => 'viewer', 'status' => 'active']);

        $response = $this->actingAs($this->admin())->put(route('admin.users.update', $user), [
            'username' => $user->username,
            'name'     => 'Updated Name',
            'role'     => 'editor',
            'status'   => 'active',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name', 'role' => 'editor']);
    }

    // ── Activate / Deactivate ────────────────────────────────────────────────

    public function test_admin_can_activate_pending_user(): void
    {
        $user = User::factory()->create(['role' => 'viewer', 'status' => 'pending']);

        $response = $this->actingAs($this->admin())->patch(route('admin.users.activate', $user));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'active']);
    }

    public function test_admin_can_deactivate_user(): void
    {
        $user = User::factory()->create(['role' => 'viewer', 'status' => 'active']);

        $response = $this->actingAs($this->admin())->patch(route('admin.users.deactivate', $user));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'inactive']);
    }

    // ── Password Reset ───────────────────────────────────────────────────────

    public function test_admin_can_reset_user_password(): void
    {
        $user = User::factory()->create(['role' => 'viewer', 'status' => 'active']);

        $response = $this->actingAs($this->admin())->patch(route('admin.users.reset-password', $user), [
            'password' => 'newpassword123',
        ]);

        $response->assertRedirect();
    }

    public function test_reset_password_requires_minimum_length(): void
    {
        $user = User::factory()->create(['role' => 'viewer', 'status' => 'active']);

        $response = $this->actingAs($this->admin())->patch(route('admin.users.reset-password', $user), [
            'password' => 'short',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    // ── Pending Users ────────────────────────────────────────────────────────

    public function test_admin_can_view_pending_users(): void
    {
        User::factory()->create(['role' => 'viewer', 'status' => 'pending']);

        $response = $this->actingAs($this->admin())->get(route('admin.users.pending'));

        $response->assertOk();
    }

    // ── Bulk Actions ─────────────────────────────────────────────────────────

    public function test_admin_can_bulk_activate_users(): void
    {
        $users = User::factory()->count(3)->create(['role' => 'viewer', 'status' => 'pending']);

        $response = $this->actingAs($this->admin())->post(route('admin.users.bulk-activate'), [
            'ids' => $users->pluck('id')->all(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $users->first()->id, 'status' => 'active']);
    }

    public function test_admin_can_bulk_reject_users(): void
    {
        $users = User::factory()->count(2)->create(['role' => 'viewer', 'status' => 'pending']);

        $response = $this->actingAs($this->admin())->post(route('admin.users.bulk-reject'), [
            'ids' => $users->pluck('id')->all(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $users->first()->id]);
    }
}
