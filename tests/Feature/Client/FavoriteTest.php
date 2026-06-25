<?php

namespace Tests\Feature\Client;

use App\Models\Favorite;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create(['role' => 'viewer', 'status' => 'active']);
    }

    public function test_favorites_index_requires_authentication(): void
    {
        $this->get(route('favorites.index'))->assertRedirect(route('login'));
    }

    public function test_favorites_index_shows_only_user_favorites(): void
    {
        $user  = $this->user();
        $other = $this->user();

        $mine  = Resource::factory()->create(['title' => 'My Favorite Doc', 'status' => 'published']);
        $theirs = Resource::factory()->create(['title' => 'Their Favorite Doc', 'status' => 'published']);

        Favorite::create(['user_id' => $user->id,  'resource_id' => $mine->id]);
        Favorite::create(['user_id' => $other->id, 'resource_id' => $theirs->id]);

        $this->actingAs($user)
            ->get(route('favorites.index'))
            ->assertOk()
            ->assertSeeText('My Favorite Doc')
            ->assertDontSeeText('Their Favorite Doc');
    }

    public function test_store_adds_resource_to_favorites(): void
    {
        $user     = $this->user();
        $resource = Resource::factory()->create(['status' => 'published']);

        $this->actingAs($user)
            ->post(route('favorites.store', $resource))
            ->assertRedirect();

        $this->assertDatabaseHas('favorites', [
            'user_id'     => $user->id,
            'resource_id' => $resource->id,
        ]);
    }

    public function test_destroy_removes_resource_from_favorites(): void
    {
        $user     = $this->user();
        $resource = Resource::factory()->create(['status' => 'published']);

        Favorite::create(['user_id' => $user->id, 'resource_id' => $resource->id]);

        $this->actingAs($user)
            ->delete(route('favorites.destroy', $resource))
            ->assertRedirect();

        $this->assertDatabaseMissing('favorites', [
            'user_id'     => $user->id,
            'resource_id' => $resource->id,
        ]);
    }

    public function test_store_requires_authentication(): void
    {
        $resource = Resource::factory()->create(['status' => 'published']);
        $this->post(route('favorites.store', $resource))->assertRedirect(route('login'));
    }

    public function test_destroy_requires_authentication(): void
    {
        $resource = Resource::factory()->create(['status' => 'published']);
        $this->delete(route('favorites.destroy', $resource))->assertRedirect(route('login'));
    }
}
