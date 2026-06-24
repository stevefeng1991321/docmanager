<?php

namespace Tests\Feature\Admin;

use App\Models\Resource;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
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

    // ── Index ────────────────────────────────────────────────────────────────

    public function test_admin_can_view_tags(): void
    {
        $response = $this->actingAs($this->admin())->get(route('admin.tags.index'));

        $response->assertOk();
    }

    public function test_editor_can_view_tags(): void
    {
        $response = $this->actingAs($this->editor())->get(route('admin.tags.index'));

        $response->assertOk();
    }

    // ── Store ────────────────────────────────────────────────────────────────

    public function test_admin_can_create_tag(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.tags.store'), [
            'name' => 'laravel',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tags', ['name' => 'laravel']);
    }

    public function test_store_rejects_duplicate_tag_name(): void
    {
        Tag::factory()->create(['name' => 'existing']);

        $response = $this->actingAs($this->admin())->post(route('admin.tags.store'), [
            'name' => 'existing',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_store_validates_required_name(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.tags.store'), []);

        $response->assertSessionHasErrors(['name']);
    }

    // ── Update ───────────────────────────────────────────────────────────────

    public function test_admin_can_update_tag(): void
    {
        $tag = Tag::factory()->create(['name' => 'old-name']);

        $response = $this->actingAs($this->admin())->put(route('admin.tags.update', $tag), [
            'name' => 'new-name',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tags', ['id' => $tag->id, 'name' => 'new-name']);
    }

    public function test_update_allows_keeping_same_name(): void
    {
        $tag = Tag::factory()->create(['name' => 'same']);

        $response = $this->actingAs($this->admin())->put(route('admin.tags.update', $tag), [
            'name' => 'same',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    // ── Destroy ──────────────────────────────────────────────────────────────

    public function test_admin_can_delete_tag(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->actingAs($this->admin())->delete(route('admin.tags.destroy', $tag));

        $response->assertRedirect();
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    // ── Merge ────────────────────────────────────────────────────────────────

    public function test_admin_can_merge_tags(): void
    {
        $source = Tag::factory()->create();
        $target = Tag::factory()->create();
        $doc    = Resource::factory()->create();
        $doc->tags()->attach($source->id);

        $response = $this->actingAs($this->admin())->post(route('admin.tags.merge'), [
            'source_id' => $source->id,
            'target_id' => $target->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('tags', ['id' => $source->id]);
        $this->assertDatabaseHas('resource_tags', ['resource_id' => $doc->id, 'tag_id' => $target->id]);
    }

    public function test_merge_requires_different_source_and_target(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->actingAs($this->admin())->post(route('admin.tags.merge'), [
            'source_id' => $tag->id,
            'target_id' => $tag->id,
        ]);

        $response->assertSessionHasErrors(['target_id']);
    }

    public function test_merge_validates_source_exists(): void
    {
        $target = Tag::factory()->create();

        $response = $this->actingAs($this->admin())->post(route('admin.tags.merge'), [
            'source_id' => 9999,
            'target_id' => $target->id,
        ]);

        $response->assertSessionHasErrors(['source_id']);
    }
}
