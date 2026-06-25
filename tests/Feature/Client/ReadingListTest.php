<?php

namespace Tests\Feature\Client;

use App\Models\ReadingList;
use App\Models\ReadingListItem;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingListTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create(['role' => 'viewer', 'status' => 'active']);
    }

    public function test_index_requires_authentication(): void
    {
        $this->get(route('reading-lists.index'))->assertRedirect(route('login'));
    }

    public function test_index_shows_only_own_lists(): void
    {
        $user  = $this->user();
        $other = $this->user();

        ReadingList::create(['user_id' => $user->id,  'name' => 'My List']);
        ReadingList::create(['user_id' => $other->id, 'name' => 'Their List']);

        $this->actingAs($user)
            ->get(route('reading-lists.index'))
            ->assertOk()
            ->assertSeeText('My List')
            ->assertDontSeeText('Their List');
    }

    public function test_store_creates_reading_list(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->post(route('reading-lists.store'), ['name' => 'My New List'])
            ->assertRedirect();

        $this->assertDatabaseHas('reading_lists', [
            'user_id' => $user->id,
            'name'    => 'My New List',
        ]);
    }

    public function test_store_validates_name_required(): void
    {
        $this->actingAs($this->user())
            ->post(route('reading-lists.store'), ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    public function test_show_returns_403_for_other_user_list(): void
    {
        $user  = $this->user();
        $other = $this->user();
        $list  = ReadingList::create(['user_id' => $other->id, 'name' => 'Private List']);

        $this->actingAs($user)
            ->get(route('reading-lists.show', $list))
            ->assertForbidden();
    }

    public function test_update_changes_list_name(): void
    {
        $user = $this->user();
        $list = ReadingList::create(['user_id' => $user->id, 'name' => 'Old Name']);

        $this->actingAs($user)
            ->put(route('reading-lists.update', $list), ['name' => 'New Name'])
            ->assertRedirect();

        $this->assertDatabaseHas('reading_lists', ['id' => $list->id, 'name' => 'New Name']);
    }

    public function test_update_returns_403_for_other_user_list(): void
    {
        $user  = $this->user();
        $other = $this->user();
        $list  = ReadingList::create(['user_id' => $other->id, 'name' => 'Their List']);

        $this->actingAs($user)
            ->put(route('reading-lists.update', $list), ['name' => 'Hijacked'])
            ->assertForbidden();
    }

    public function test_destroy_deletes_reading_list(): void
    {
        $user = $this->user();
        $list = ReadingList::create(['user_id' => $user->id, 'name' => 'To Delete']);

        $this->actingAs($user)
            ->delete(route('reading-lists.destroy', $list))
            ->assertRedirect(route('reading-lists.index'));

        $this->assertDatabaseMissing('reading_lists', ['id' => $list->id]);
    }

    public function test_add_item_adds_resource_to_list(): void
    {
        $user     = $this->user();
        $list     = ReadingList::create(['user_id' => $user->id, 'name' => 'Test List']);
        $resource = Resource::factory()->create(['status' => 'published']);

        $this->actingAs($user)
            ->post(route('reading-lists.items.add', [$list, $resource]))
            ->assertRedirect();

        $this->assertDatabaseHas('reading_list_items', [
            'reading_list_id' => $list->id,
            'resource_id'     => $resource->id,
        ]);
    }

    public function test_add_item_is_idempotent(): void
    {
        $user     = $this->user();
        $list     = ReadingList::create(['user_id' => $user->id, 'name' => 'Test List']);
        $resource = Resource::factory()->create(['status' => 'published']);

        $this->actingAs($user)->post(route('reading-lists.items.add', [$list, $resource]));
        $this->actingAs($user)->post(route('reading-lists.items.add', [$list, $resource]));

        $this->assertEquals(1, ReadingListItem::where('reading_list_id', $list->id)
            ->where('resource_id', $resource->id)->count());
    }

    public function test_remove_item_removes_resource_from_list(): void
    {
        $user     = $this->user();
        $list     = ReadingList::create(['user_id' => $user->id, 'name' => 'Test List']);
        $resource = Resource::factory()->create(['status' => 'published']);

        ReadingListItem::create([
            'reading_list_id' => $list->id,
            'resource_id'     => $resource->id,
            'sort_order'      => 1,
            'added_at'        => now(),
        ]);

        $this->actingAs($user)
            ->delete(route('reading-lists.items.remove', [$list, $resource]))
            ->assertRedirect();

        $this->assertDatabaseMissing('reading_list_items', [
            'reading_list_id' => $list->id,
            'resource_id'     => $resource->id,
        ]);
    }

    public function test_add_item_returns_403_for_other_user_list(): void
    {
        $user     = $this->user();
        $other    = $this->user();
        $list     = ReadingList::create(['user_id' => $other->id, 'name' => 'Theirs']);
        $resource = Resource::factory()->create(['status' => 'published']);

        $this->actingAs($user)
            ->post(route('reading-lists.items.add', [$list, $resource]))
            ->assertForbidden();
    }
}
