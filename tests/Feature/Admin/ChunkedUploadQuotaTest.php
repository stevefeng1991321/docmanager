<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ChunkedUploadQuotaTest extends TestCase
{
    use RefreshDatabase;

    private function editor(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'role'   => 'editor',
            'status' => 'active',
        ], $attrs));
    }

    private function chunk(): UploadedFile
    {
        return UploadedFile::fake()->create('chunk.bin', 512); // 512 KB chunk
    }

    // ── Chunk endpoint ───────────────────────────────────────────────────────

    public function test_first_chunk_accepted_when_within_quota(): void
    {
        Storage::fake('local');
        $user = $this->editor(['storage_quota_mb' => 100]); // 100 MB quota, no usage

        $response = $this->actingAs($user)->post(route('admin.documents.upload.chunk'), [
            'file_id'      => 'test-file-abc123',
            'chunk_index'  => 0,
            'total_chunks' => 3,
            'chunk'        => $this->chunk(),
            'file_size'    => 1 * 1024 * 1024, // 1 MB — well within 100 MB
        ]);

        $response->assertOk()
            ->assertJsonPath('received', 1);
    }

    public function test_first_chunk_rejected_when_quota_exceeded(): void
    {
        Storage::fake('local');

        // User has 100 MB quota and has already used 99 MB
        $user = $this->editor(['storage_quota_mb' => 100]);
        // Simulate usage by creating a resource record
        \App\Models\Resource::factory()->create([
            'uploaded_by' => $user->id,
            'file_size'   => 99 * 1024 * 1024, // 99 MB used
        ]);

        $response = $this->actingAs($user)->post(route('admin.documents.upload.chunk'), [
            'file_id'      => 'test-file-xyz789',
            'chunk_index'  => 0,
            'total_chunks' => 2,
            'chunk'        => $this->chunk(),
            'file_size'    => 5 * 1024 * 1024, // 5 MB — would push to 104 MB
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error', 'This upload would exceed your storage quota.');
    }

    public function test_non_first_chunk_skips_quota_check(): void
    {
        Storage::fake('local');

        // User is at quota limit — but this is not chunk 0, so no check
        $user = $this->editor(['storage_quota_mb' => 10]);
        \App\Models\Resource::factory()->create([
            'uploaded_by' => $user->id,
            'file_size'   => 10 * 1024 * 1024, // fully used
        ]);

        $response = $this->actingAs($user)->post(route('admin.documents.upload.chunk'), [
            'file_id'      => 'test-file-continuation',
            'chunk_index'  => 1, // NOT the first chunk
            'total_chunks' => 4,
            'chunk'        => $this->chunk(),
            'file_size'    => 5 * 1024 * 1024,
        ]);

        $response->assertOk(); // no quota gate on continuation chunks
    }

    public function test_no_quota_set_allows_any_upload(): void
    {
        Storage::fake('local');
        $user = $this->editor(['storage_quota_mb' => null]); // unlimited

        $response = $this->actingAs($user)->post(route('admin.documents.upload.chunk'), [
            'file_id'      => 'test-file-unlimited',
            'chunk_index'  => 0,
            'total_chunks' => 1,
            'chunk'        => $this->chunk(),
            'file_size'    => 999 * 1024 * 1024, // 999 MB — no limit
        ]);

        $response->assertOk();
    }

    public function test_first_chunk_without_file_size_skips_quota_check(): void
    {
        Storage::fake('local');
        $user = $this->editor(['storage_quota_mb' => 1]); // tiny quota

        $response = $this->actingAs($user)->post(route('admin.documents.upload.chunk'), [
            'file_id'      => 'test-file-nosize',
            'chunk_index'  => 0,
            'total_chunks' => 1,
            'chunk'        => $this->chunk(),
            // file_size intentionally omitted
        ]);

        // No file_size = can't check quota = should proceed
        $response->assertOk();
    }

    public function test_chunk_upload_requires_authentication(): void
    {
        $response = $this->post(route('admin.documents.upload.chunk'), [
            'file_id'      => 'test-anon',
            'chunk_index'  => 0,
            'total_chunks' => 1,
            'chunk'        => $this->chunk(),
        ]);

        $response->assertRedirect(route('login'));
    }
}
