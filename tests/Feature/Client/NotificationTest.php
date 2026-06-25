<?php

namespace Tests\Feature\Client;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create(['role' => 'viewer', 'status' => 'active']);
    }

    private function createNotification(int $userId, bool $isRead = false): Notification
    {
        return Notification::create([
            'user_id'    => $userId,
            'type'       => 'general',
            'title'      => 'Test Notification',
            'message'    => 'This is a test notification message.',
            'is_read'    => $isRead,
            'created_at' => now(),
        ]);
    }

    public function test_index_requires_authentication(): void
    {
        $this->get(route('notifications.index'))->assertRedirect(route('login'));
    }

    public function test_index_shows_only_own_notifications(): void
    {
        $user  = $this->user();
        $other = $this->user();

        Notification::create([
            'user_id' => $user->id,  'type' => 'general',
            'title' => 'My Notification', 'message' => 'For me.', 'is_read' => false, 'created_at' => now(),
        ]);
        Notification::create([
            'user_id' => $other->id, 'type' => 'general',
            'title' => 'Their Notification', 'message' => 'For them.', 'is_read' => false, 'created_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSeeText('My Notification')
            ->assertDontSeeText('Their Notification');
    }

    public function test_mark_read_sets_is_read_true(): void
    {
        $user   = $this->user();
        $notif  = $this->createNotification($user->id, false);

        $this->actingAs($user)
            ->patch(route('notifications.read', $notif))
            ->assertRedirect();

        $this->assertDatabaseHas('notifications', ['id' => $notif->id, 'is_read' => true]);
    }

    public function test_mark_read_returns_403_for_other_users_notification(): void
    {
        $user  = $this->user();
        $other = $this->user();
        $notif = $this->createNotification($other->id, false);

        $this->actingAs($user)
            ->patch(route('notifications.read', $notif))
            ->assertForbidden();
    }

    public function test_mark_all_read_marks_all_unread_notifications(): void
    {
        $user = $this->user();

        $this->createNotification($user->id, false);
        $this->createNotification($user->id, false);
        $this->createNotification($user->id, true);

        $this->actingAs($user)
            ->patch(route('notifications.read-all'))
            ->assertRedirect();

        $unread = Notification::where('user_id', $user->id)->where('is_read', false)->count();
        $this->assertEquals(0, $unread);
    }

    public function test_mark_all_read_does_not_affect_other_users_notifications(): void
    {
        $user  = $this->user();
        $other = $this->user();

        $this->createNotification($user->id, false);
        $otherNotif = $this->createNotification($other->id, false);

        $this->actingAs($user)->patch(route('notifications.read-all'));

        $this->assertDatabaseHas('notifications', ['id' => $otherNotif->id, 'is_read' => false]);
    }

    public function test_destroy_deletes_own_notification(): void
    {
        $user  = $this->user();
        $notif = $this->createNotification($user->id);

        $this->actingAs($user)
            ->delete(route('notifications.destroy', $notif))
            ->assertRedirect();

        $this->assertDatabaseMissing('notifications', ['id' => $notif->id]);
    }

    public function test_destroy_returns_403_for_other_users_notification(): void
    {
        $user  = $this->user();
        $other = $this->user();
        $notif = $this->createNotification($other->id);

        $this->actingAs($user)
            ->delete(route('notifications.destroy', $notif))
            ->assertForbidden();
    }
}
