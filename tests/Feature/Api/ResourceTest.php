<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Resource;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResourceTest extends TestCase
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

    public function test_authenticated_user_can_list_resources(): void
    {
        Resource::factory()->count(3)->create(['status' => 'published']);

        $response = $this->actingAs($this->viewer())->getJson('/api/resources');

        $response->assertOk()
            ->assertJsonStructure(['data', 'meta', 'links']);
    }

    public function test_list_returns_only_published_by_default(): void
    {
        Resource::factory()->create(['status' => 'published']);
        Resource::factory()->draft()->create();

        $response = $this->actingAs($this->viewer())->getJson('/api/resources');

        $data = $response->json('data');
        $this->assertCount(1, $data);
    }

    public function test_list_can_filter_by_category(): void
    {
        $category = Category::factory()->create();
        Resource::factory()->forCategory($category)->create(['status' => 'published']);
        Resource::factory()->create(['status' => 'published']);

        $response = $this->actingAs($this->viewer())->getJson('/api/resources?category_id=' . $category->id);

        $this->assertCount(1, $response->json('data'));
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $response = $this->getJson('/api/resources');

        $response->assertUnauthorized();
    }

    // ── Show ─────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_view_published_resource(): void
    {
        $resource = Resource::factory()->create(['status' => 'published']);

        $response = $this->actingAs($this->viewer())->getJson('/api/resources/' . $resource->id);

        $response->assertOk()
            ->assertJsonPath('data.id', $resource->id)
            ->assertJsonPath('data.title', $resource->title);
    }

    public function test_viewer_cannot_view_draft_resource(): void
    {
        $resource = Resource::factory()->draft()->create();

        $response = $this->actingAs($this->viewer())->getJson('/api/resources/' . $resource->id);

        $response->assertNotFound();
    }

    public function test_editor_can_view_draft_resource(): void
    {
        $resource = Resource::factory()->draft()->create();

        $response = $this->actingAs($this->editor())->getJson('/api/resources/' . $resource->id);

        $response->assertOk();
    }

    // ── Store ────────────────────────────────────────────────────────────────

    public function test_editor_can_create_resource(): void
    {
        Storage::fake('local');
        $category = Category::factory()->create();

        $response = $this->actingAs($this->editor())->postJson('/api/resources', [
            'title'       => 'New API Document',
            'description' => 'Created via API',
            'file'        => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'),
            'category_id' => $category->id,
            'status'      => 'draft',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'New API Document');

        $this->assertDatabaseHas('resources', ['title' => 'New API Document']);
    }

    public function test_viewer_cannot_create_resource(): void
    {
        Storage::fake('local');

        $response = $this->actingAs($this->viewer())->postJson('/api/resources', [
            'title'  => 'Denied',
            'file'   => UploadedFile::fake()->create('doc.pdf', 100),
            'status' => 'draft',
        ]);

        $response->assertForbidden();
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->editor())->postJson('/api/resources', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'file']);
    }

    public function test_store_validates_category_exists(): void
    {
        Storage::fake('local');

        $response = $this->actingAs($this->editor())->postJson('/api/resources', [
            'title'       => 'Test',
            'file'        => UploadedFile::fake()->create('doc.pdf', 100),
            'category_id' => 9999,
            'status'      => 'draft',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['category_id']);
    }

    // ── Update ───────────────────────────────────────────────────────────────

    public function test_editor_can_update_resource(): void
    {
        $resource = Resource::factory()->create(['status' => 'published']);

        $response = $this->actingAs($this->editor())->putJson('/api/resources/' . $resource->id, [
            'title' => 'Updated via API',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.title', 'Updated via API');

        $this->assertDatabaseHas('resources', ['id' => $resource->id, 'title' => 'Updated via API']);
    }

    public function test_viewer_cannot_update_resource(): void
    {
        $resource = Resource::factory()->create(['status' => 'published']);

        $response = $this->actingAs($this->viewer())->putJson('/api/resources/' . $resource->id, [
            'title' => 'Should fail',
        ]);

        $response->assertForbidden();
    }

    public function test_update_validates_status_values(): void
    {
        $resource = Resource::factory()->create(['status' => 'published']);

        $response = $this->actingAs($this->editor())->putJson('/api/resources/' . $resource->id, [
            'status' => 'invalid_status',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }

    // ── Destroy ──────────────────────────────────────────────────────────────

    public function test_editor_can_delete_resource(): void
    {
        $resource = Resource::factory()->create(['status' => 'published']);

        $response = $this->actingAs($this->editor())->deleteJson('/api/resources/' . $resource->id);

        $response->assertOk()
            ->assertJsonFragment(['message' => 'Document deleted.']);

        $this->assertSoftDeleted('resources', ['id' => $resource->id]);
    }

    public function test_viewer_cannot_delete_resource(): void
    {
        $resource = Resource::factory()->create(['status' => 'published']);

        $response = $this->actingAs($this->viewer())->deleteJson('/api/resources/' . $resource->id);

        $response->assertForbidden();
    }

    // ── Share ────────────────────────────────────────────────────────────────

    public function test_user_can_create_share_link(): void
    {
        $resource = Resource::factory()->create(['status' => 'published']);

        $response = $this->actingAs($this->viewer())->postJson('/api/resources/' . $resource->id . '/share');

        $response->assertOk()
            ->assertJsonStructure(['share_url', 'expires_at']);
    }

    public function test_cannot_share_unpublished_resource(): void
    {
        $resource = Resource::factory()->draft()->create();

        $response = $this->actingAs($this->viewer())->postJson('/api/resources/' . $resource->id . '/share');

        $response->assertNotFound();
    }
}
