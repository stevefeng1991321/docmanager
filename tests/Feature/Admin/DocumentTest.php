<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Resource;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentTest extends TestCase
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

    private function viewer(): User
    {
        return User::factory()->create(['role' => 'viewer', 'status' => 'active']);
    }

    // ── Index ────────────────────────────────────────────────────────────────

    public function test_admin_can_view_document_list(): void
    {
        Resource::factory()->count(3)->create();

        $response = $this->actingAs($this->admin())->get(route('admin.documents.index'));

        $response->assertOk();
    }

    public function test_document_list_filters_by_status(): void
    {
        Resource::factory()->create(['status' => 'published']);
        Resource::factory()->draft()->create();

        $response = $this->actingAs($this->admin())->get(route('admin.documents.index', ['status' => 'draft']));

        $response->assertOk();
    }

    public function test_viewer_cannot_access_admin_documents(): void
    {
        $response = $this->actingAs($this->viewer())->get(route('admin.documents.index'));

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_from_admin(): void
    {
        $response = $this->get(route('admin.documents.index'));

        $response->assertRedirect('/login');
    }

    // ── Create / Store ───────────────────────────────────────────────────────

    public function test_admin_can_view_create_form(): void
    {
        $response = $this->actingAs($this->admin())->get(route('admin.documents.create'));

        $response->assertOk();
    }

    public function test_admin_can_upload_document(): void
    {
        Storage::fake('local');
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin())->post(route('admin.documents.store'), [
            'title'       => 'Test Document',
            'description' => 'A test description',
            'file'        => UploadedFile::fake()->create('test.pdf', 100, 'application/pdf'),
            'category_id' => $category->id,
            'status'      => 'published',
        ]);

        $response->assertRedirect(route('admin.documents.index'));
        $this->assertDatabaseHas('resources', ['title' => 'Test Document']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.documents.store'), []);

        $response->assertSessionHasErrors(['title', 'file', 'status']);
    }

    public function test_store_validates_category_exists(): void
    {
        Storage::fake('local');

        $response = $this->actingAs($this->admin())->post(route('admin.documents.store'), [
            'title'       => 'Test',
            'file'        => UploadedFile::fake()->create('test.pdf', 100),
            'category_id' => 9999,
            'status'      => 'draft',
        ]);

        $response->assertSessionHasErrors(['category_id']);
    }

    // ── Edit / Update ────────────────────────────────────────────────────────

    public function test_admin_can_view_edit_form(): void
    {
        $document = Resource::factory()->create();

        $response = $this->actingAs($this->admin())->get(route('admin.documents.edit', $document));

        $response->assertOk();
    }

    public function test_admin_can_update_document(): void
    {
        $document = Resource::factory()->create();

        $response = $this->actingAs($this->admin())->put(route('admin.documents.update', $document), [
            'title'       => 'Updated Title',
            'description' => 'Updated description',
        ]);

        $response->assertRedirect(route('admin.documents.edit', $document));
        $this->assertDatabaseHas('resources', ['id' => $document->id, 'title' => 'Updated Title']);
    }

    public function test_update_validates_required_title(): void
    {
        $document = Resource::factory()->create();

        $response = $this->actingAs($this->admin())->put(route('admin.documents.update', $document), [
            'title' => '',
        ]);

        $response->assertSessionHasErrors(['title']);
    }

    // ── Destroy / Trash / Restore ────────────────────────────────────────────

    public function test_admin_can_soft_delete_document(): void
    {
        $document = Resource::factory()->create();

        $response = $this->actingAs($this->admin())->delete(route('admin.documents.destroy', $document));

        $response->assertRedirect(route('admin.documents.index'));
        $this->assertSoftDeleted('resources', ['id' => $document->id]);
    }

    public function test_admin_can_view_trash(): void
    {
        Resource::factory()->create()->delete();

        $response = $this->actingAs($this->admin())->get(route('admin.documents.trash'));

        $response->assertOk();
    }

    public function test_admin_can_restore_trashed_document(): void
    {
        $document = Resource::factory()->create();
        $document->delete();

        $response = $this->actingAs($this->admin())->patch(route('admin.documents.restore', $document->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('resources', ['id' => $document->id, 'deleted_at' => null]);
    }

    public function test_admin_can_force_delete_document(): void
    {
        Storage::fake('local');
        $document = Resource::factory()->create();
        $document->delete();

        $response = $this->actingAs($this->admin())->delete(route('admin.documents.force-delete', $document->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('resources', ['id' => $document->id]);
    }

    // ── Lock / Unlock ────────────────────────────────────────────────────────

    public function test_admin_can_lock_document(): void
    {
        $document = Resource::factory()->create();
        $admin    = $this->admin();

        $response = $this->actingAs($admin)->patch(route('admin.documents.lock', $document));

        $response->assertRedirect();
        $this->assertDatabaseHas('resources', ['id' => $document->id, 'locked_by' => $admin->id]);
    }

    public function test_admin_can_unlock_document(): void
    {
        $admin    = $this->admin();
        $document = Resource::factory()->create(['locked_by' => $admin->id, 'locked_at' => now()]);

        $response = $this->actingAs($admin)->patch(route('admin.documents.unlock', $document));

        $response->assertRedirect();
        $this->assertDatabaseHas('resources', ['id' => $document->id, 'locked_by' => null]);
    }

    // ── Approve / Reject ─────────────────────────────────────────────────────

    public function test_admin_can_approve_document(): void
    {
        $document = Resource::factory()->pendingReview()->create();

        $response = $this->actingAs($this->admin())->patch(route('admin.documents.approve', $document));

        $response->assertRedirect();
        $this->assertDatabaseHas('resources', ['id' => $document->id, 'status' => 'published']);
    }

    public function test_admin_can_reject_document(): void
    {
        $document = Resource::factory()->pendingReview()->create();

        $response = $this->actingAs($this->admin())->patch(route('admin.documents.reject', $document), [
            'reason' => 'Not suitable content',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('resources', ['id' => $document->id, 'status' => 'rejected']);
    }

    // ── Bulk Actions ─────────────────────────────────────────────────────────

    public function test_admin_can_bulk_approve_documents(): void
    {
        $docs = Resource::factory()->pendingReview()->count(3)->create();

        $response = $this->actingAs($this->admin())->post(route('admin.documents.bulk-approve'), [
            'ids' => $docs->pluck('id')->all(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('resources', ['id' => $docs->first()->id, 'status' => 'published']);
    }

    public function test_admin_can_bulk_trash_documents(): void
    {
        $docs = Resource::factory()->count(2)->create();

        $response = $this->actingAs($this->admin())->post(route('admin.documents.bulk-trash'), [
            'ids' => $docs->pluck('id')->all(),
        ]);

        $response->assertRedirect();
        $this->assertSoftDeleted('resources', ['id' => $docs->first()->id]);
    }

    public function test_admin_can_bulk_assign_category(): void
    {
        $docs     = Resource::factory()->count(2)->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin())->post(route('admin.documents.bulk-assign-category'), [
            'ids'         => $docs->pluck('id')->all(),
            'category_id' => $category->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('resources', ['id' => $docs->first()->id, 'category_id' => $category->id]);
    }
}
