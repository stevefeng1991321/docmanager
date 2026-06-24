<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
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

    public function test_admin_can_view_categories(): void
    {
        $response = $this->actingAs($this->admin())->get(route('admin.categories.index'));

        $response->assertOk();
    }

    public function test_editor_can_view_categories(): void
    {
        $response = $this->actingAs($this->editor())->get(route('admin.categories.index'));

        $response->assertOk();
    }

    // ── Store ────────────────────────────────────────────────────────────────

    public function test_admin_can_create_category(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.categories.store'), [
            'name' => 'Engineering',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Engineering']);
    }

    public function test_admin_can_create_subcategory(): void
    {
        $parent = Category::factory()->create();

        $response = $this->actingAs($this->admin())->post(route('admin.categories.store'), [
            'name'      => 'Sub Category',
            'parent_id' => $parent->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Sub Category', 'parent_id' => $parent->id]);
    }

    public function test_store_validates_required_name(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.categories.store'), []);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_store_validates_parent_exists(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.categories.store'), [
            'name'      => 'Test',
            'parent_id' => 9999,
        ]);

        $response->assertSessionHasErrors(['parent_id']);
    }

    // ── Update ───────────────────────────────────────────────────────────────

    public function test_admin_can_update_category(): void
    {
        $category = Category::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->admin())->put(route('admin.categories.update', $category), [
            'name' => 'New Name',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'New Name']);
    }

    public function test_cannot_set_category_as_own_parent(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin())->put(route('admin.categories.update', $category), [
            'name'      => 'Same',
            'parent_id' => $category->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'error');
    }

    // ── Destroy ──────────────────────────────────────────────────────────────

    public function test_admin_can_delete_empty_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin())->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_cannot_delete_category_with_documents(): void
    {
        $category = Category::factory()->create();
        Resource::factory()->forCategory($category)->create();

        $response = $this->actingAs($this->admin())->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_cannot_delete_category_with_subcategories(): void
    {
        $parent = Category::factory()->create();
        Category::factory()->create(['parent_id' => $parent->id]);

        $response = $this->actingAs($this->admin())->delete(route('admin.categories.destroy', $parent));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'error');
        $this->assertDatabaseHas('categories', ['id' => $parent->id]);
    }
}
