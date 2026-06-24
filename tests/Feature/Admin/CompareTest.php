<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompareTest extends TestCase
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

    private function doc(array $attrs = []): Resource
    {
        return Resource::factory()->create(array_merge(['status' => 'published'], $attrs));
    }

    // ── Index ────────────────────────────────────────────────────────────────

    public function test_admin_can_view_compare_index(): void
    {
        $response = $this->actingAs($this->admin())->get(route('admin.compare.index'));

        $response->assertOk();
    }

    public function test_editor_can_view_compare_index(): void
    {
        $response = $this->actingAs($this->editor())->get(route('admin.compare.index'));

        $response->assertOk();
    }

    public function test_guest_cannot_view_compare_index(): void
    {
        $response = $this->get(route('admin.compare.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_index_redirects_to_show_when_both_params_given(): void
    {
        $a = $this->doc();
        $b = $this->doc();

        $response = $this->actingAs($this->admin())
            ->get(route('admin.compare.index', ['a' => $a->id, 'b' => $b->id]));

        $response->assertRedirect(route('admin.compare.show', [$a->id, $b->id]));
    }

    public function test_index_stays_on_page_when_both_params_are_same(): void
    {
        $a = $this->doc();

        $response = $this->actingAs($this->admin())
            ->get(route('admin.compare.index', ['a' => $a->id, 'b' => $a->id]));

        $response->assertOk();
    }

    // ── Show ─────────────────────────────────────────────────────────────────

    public function test_admin_can_compare_two_documents(): void
    {
        $a = $this->doc(['title' => 'Document Alpha', 'content' => "line one\nline two"]);
        $b = $this->doc(['title' => 'Document Beta',  'content' => "line one\nline three"]);

        $response = $this->actingAs($this->admin())
            ->get(route('admin.compare.show', [$a->id, $b->id]));

        $response->assertOk()
            ->assertSee('Document Alpha')
            ->assertSee('Document Beta');
    }

    public function test_compare_show_highlights_differing_metadata(): void
    {
        $cat = Category::factory()->create(['name' => 'Science']);
        $a   = $this->doc(['title' => 'Alpha', 'category_id' => $cat->id]);
        $b   = $this->doc(['title' => 'Beta',  'category_id' => null]);

        $response = $this->actingAs($this->admin())
            ->get(route('admin.compare.show', [$a->id, $b->id]));

        $response->assertOk()
            ->assertSee('Alpha')
            ->assertSee('Beta');
    }

    public function test_compare_show_handles_documents_with_no_content(): void
    {
        $a = $this->doc(['content' => null]);
        $b = $this->doc(['content' => null]);

        $response = $this->actingAs($this->admin())
            ->get(route('admin.compare.show', [$a->id, $b->id]));

        $response->assertOk()
            ->assertSee('No extracted text available');
    }

    public function test_compare_show_handles_identical_content(): void
    {
        $a = $this->doc(['content' => "same line\nanother line"]);
        $b = $this->doc(['content' => "same line\nanother line"]);

        $response = $this->actingAs($this->admin())
            ->get(route('admin.compare.show', [$a->id, $b->id]));

        $response->assertOk();
    }

    public function test_swap_reverses_documents(): void
    {
        $a = $this->doc(['title' => 'First Doc']);
        $b = $this->doc(['title' => 'Second Doc']);

        $forward  = $this->actingAs($this->admin())
            ->get(route('admin.compare.show', [$a->id, $b->id]));
        $swapped  = $this->actingAs($this->admin())
            ->get(route('admin.compare.show', [$b->id, $a->id]));

        $forward->assertOk();
        $swapped->assertOk();
        // Both orderings render without error
        $forward->assertSee('First Doc');
        $swapped->assertSee('First Doc');
    }
}
