<?php

namespace Tests\Unit\Models;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationPreferenceTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create(['role' => 'viewer', 'status' => 'active']);
    }

    public function test_notification_sent_when_no_preference_row_exists(): void
    {
        $user = $this->user();

        Notification::send($user->id, 'doc_approved', 'Published', 'Your doc is live.');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'type'    => 'doc_approved',
        ]);
    }

    public function test_notification_sent_when_preference_enabled(): void
    {
        $user = $this->user();
        UserPreference::create(['user_id' => $user->id, 'notify_doc_approved' => true]);

        Notification::send($user->id, 'doc_approved', 'Published');

        $this->assertDatabaseHas('notifications', ['user_id' => $user->id, 'type' => 'doc_approved']);
    }

    public function test_notification_suppressed_when_preference_disabled(): void
    {
        $user = $this->user();
        UserPreference::create(['user_id' => $user->id, 'notify_doc_approved' => false]);

        Notification::send($user->id, 'doc_approved', 'Published');

        $this->assertDatabaseMissing('notifications', ['user_id' => $user->id, 'type' => 'doc_approved']);
    }

    public function test_doc_rejected_respects_doc_approved_preference(): void
    {
        $user = $this->user();
        UserPreference::create(['user_id' => $user->id, 'notify_doc_approved' => false]);

        Notification::send($user->id, 'doc_rejected', 'Rejected');

        $this->assertDatabaseMissing('notifications', ['user_id' => $user->id, 'type' => 'doc_rejected']);
    }

    public function test_account_activated_respects_preference(): void
    {
        $user = $this->user();
        UserPreference::create(['user_id' => $user->id, 'notify_account_activated' => false]);

        Notification::send($user->id, 'account_activated', 'Active');

        $this->assertDatabaseMissing('notifications', ['user_id' => $user->id, 'type' => 'account_activated']);
    }

    public function test_system_broadcast_always_sent_regardless_of_preferences(): void
    {
        $user = $this->user();
        // Even with all prefs off, system_broadcast has no preference key → always sends
        UserPreference::create([
            'user_id'                  => $user->id,
            'notify_doc_approved'      => false,
            'notify_file_uploaded'     => false,
            'notify_account_activated' => false,
        ]);

        Notification::send($user->id, 'system_broadcast', 'Maintenance tonight');

        $this->assertDatabaseHas('notifications', ['user_id' => $user->id, 'type' => 'system_broadcast']);
    }

    public function test_account_rejected_always_sent(): void
    {
        $user = $this->user();
        // account_rejected is not in the prefMap → always sends
        Notification::send($user->id, 'account_rejected', 'Not approved');

        $this->assertDatabaseHas('notifications', ['user_id' => $user->id, 'type' => 'account_rejected']);
    }
}
