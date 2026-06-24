<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    // ── Role helpers ─────────────────────────────────────────────────────────

    public function test_isAdmin_returns_true_for_admin_role(): void
    {
        $user = User::factory()->make(['role' => 'admin']);
        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isEditor());
        $this->assertFalse($user->isViewer());
    }

    public function test_isEditor_returns_true_for_editor_role(): void
    {
        $user = User::factory()->make(['role' => 'editor']);
        $this->assertTrue($user->isEditor());
        $this->assertFalse($user->isAdmin());
    }

    public function test_isViewer_returns_true_for_viewer_role(): void
    {
        $user = User::factory()->make(['role' => 'viewer']);
        $this->assertTrue($user->isViewer());
    }

    // ── Status helpers ───────────────────────────────────────────────────────

    public function test_isActive_returns_true_for_active_status(): void
    {
        $user = User::factory()->make(['status' => 'active']);
        $this->assertTrue($user->isActive());
        $this->assertFalse($user->isPending());
    }

    public function test_isPending_returns_true_for_pending_status(): void
    {
        $user = User::factory()->make(['status' => 'pending']);
        $this->assertTrue($user->isPending());
        $this->assertFalse($user->isActive());
    }

    // ── Lockout helpers ──────────────────────────────────────────────────────

    public function test_isLocked_returns_true_when_locked_until_is_future(): void
    {
        $user = User::factory()->make(['locked_until' => Carbon::now()->addMinutes(10)]);
        $this->assertTrue($user->isLocked());
    }

    public function test_isLocked_returns_false_when_locked_until_is_past(): void
    {
        $user = User::factory()->make(['locked_until' => Carbon::now()->subMinute()]);
        $this->assertFalse($user->isLocked());
    }

    public function test_isLocked_returns_false_when_not_locked(): void
    {
        $user = User::factory()->make(['locked_until' => null]);
        $this->assertFalse($user->isLocked());
    }

    // ── Storage quota ────────────────────────────────────────────────────────

    public function test_storageQuotaBytes_returns_null_when_quota_not_set(): void
    {
        $user = User::factory()->make(['storage_quota_mb' => null]);
        $this->assertNull($user->storageQuotaBytes());
    }

    public function test_storageQuotaBytes_converts_mb_to_bytes(): void
    {
        $user = User::factory()->make(['storage_quota_mb' => 10]);
        $this->assertEquals(10 * 1024 * 1024, $user->storageQuotaBytes());
    }

    public function test_wouldExceedQuota_returns_false_when_no_quota_set(): void
    {
        $user = User::factory()->create(['role' => 'viewer', 'status' => 'active', 'storage_quota_mb' => null]);
        $this->assertFalse($user->wouldExceedQuota(999_999_999));
    }

    public function test_wouldExceedQuota_returns_true_when_over_limit(): void
    {
        $user = User::factory()->create(['role' => 'viewer', 'status' => 'active', 'storage_quota_mb' => 1]);
        $this->assertTrue($user->wouldExceedQuota(2 * 1024 * 1024));
    }

    public function test_wouldExceedQuota_returns_false_when_within_limit(): void
    {
        $user = User::factory()->create(['role' => 'viewer', 'status' => 'active', 'storage_quota_mb' => 100]);
        $this->assertFalse($user->wouldExceedQuota(1024));
    }

    // ── Password hashing ─────────────────────────────────────────────────────

    public function test_password_is_hashed_on_create(): void
    {
        $user = User::factory()->create(['role' => 'viewer', 'status' => 'active']);
        $this->assertNotEquals('password', $user->password);
    }
}
