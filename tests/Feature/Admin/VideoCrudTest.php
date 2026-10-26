<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VideoCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Category $rubrique;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->rubrique = Category::create([
            'type' => 'video',
            'name' => 'Caravanes juridiques',
            'slug' => 'caravanes-juridiques',
            'tone' => 'amber',
        ]);
    }

    /** @return array<string, mixed> */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Caravane juridique à Tillabéri',
            'summary' => 'Une capsule de terrain.',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'category_id' => $this->rubrique->id,
            'duration' => '12:35',
            'status' => Video::STATUS_PUBLISHED,
        ], $overrides);
    }

    public function test_listing_requires_authentication(): void
    {
        $this->get('/admin/web-tv')->assertRedirect(route('admin.login'));
    }

    public function test_a_video_can_be_created_by_pasting_a_link(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/web-tv', $this->payload())
            ->assertRedirect(route('admin.webtv'));

        $video = Video::firstWhere('slug', 'caravane-juridique-a-tillaberi');

        $this->assertNotNull($video);
        // The editor pasted a URL and nothing else: the provider and its id
        // were worked out server-side.
        $this->assertSame('youtube', $video->provider);
        $this->assertSame('dQw4w9WgXcQ', $video->provider_id);
        $this->assertSame($this->rubrique->id, $video->category_id);
        $this->assertNotNull($video->published_at, 'A published capsule needs a date or it never appears.');
    }

    public function test_an_address_that_cannot_be_embedded_is_refused(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/web-tv', $this->payload(['video_url' => 'https://example.com/film.mp4']))
            ->assertSessionHasErrors('video_url');

        $this->assertSame(0, Video::count());
    }

    public function test_a_malformed_duration_is_refused(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/web-tv', $this->payload(['duration' => '12m35']))
            ->assertSessionHasErrors('duration');
    }

    public function test_a_duplicate_title_gets_its_own_slug(): void
    {
        $this->actingAs($this->admin)->post('/admin/web-tv', $this->payload());
        $this->actingAs($this->admin)->post('/admin/web-tv', $this->payload());

        $this->assertDatabaseHas('videos', ['slug' => 'caravane-juridique-a-tillaberi']);
        $this->assertDatabaseHas('videos', ['slug' => 'caravane-juridique-a-tillaberi-2']);
    }

    public function test_a_cover_is_stored_on_the_public_disk(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post('/admin/web-tv', $this->payload([
            'cover' => UploadedFile::fake()->image('vignette.jpg', 640, 360),
        ]));

        $video = Video::first();

        $this->assertNotNull($video->cover);
        Storage::disk('public')->assertExists($video->cover);
    }

    public function test_a_video_can_be_updated(): void
    {
        $video = Video::create($this->payload([
            'slug' => 'capsule',
            'provider' => 'youtube',
            'provider_id' => 'dQw4w9WgXcQ',
        ]));

        $this->actingAs($this->admin)
            ->put("/admin/web-tv/{$video->id}", $this->payload([
                'title' => 'Titre corrigé',
                'video_url' => 'https://vimeo.com/123456789',
            ]))
            ->assertRedirect(route('admin.webtv'));

        $video->refresh();

        $this->assertSame('Titre corrigé', $video->title);
        // Changing the link re-parses it: the provider follows the address.
        $this->assertSame('vimeo', $video->provider);
        $this->assertSame('123456789', $video->provider_id);
    }

    public function test_a_video_goes_to_the_corbeille_and_can_be_restored(): void
    {
        $video = Video::create($this->payload([
            'slug' => 'capsule',
            'provider' => 'youtube',
            'provider_id' => 'dQw4w9WgXcQ',
        ]));

        $this->actingAs($this->admin)->delete("/admin/web-tv/{$video->id}");
        $this->assertSoftDeleted($video);

        $this->actingAs($this->admin)->post("/admin/web-tv/{$video->id}/restaurer");
        $this->assertNotSoftDeleted($video->fresh());
    }

    public function test_a_permanent_delete_removes_the_row(): void
    {
        $video = Video::create($this->payload([
            'slug' => 'capsule',
            'provider' => 'youtube',
            'provider_id' => 'dQw4w9WgXcQ',
        ]));
        $video->delete();

        $this->actingAs($this->admin)->delete("/admin/web-tv/{$video->id}/definitif");

        $this->assertDatabaseMissing('videos', ['id' => $video->id]);
    }

    public function test_the_listing_filters_on_status_and_rubrique(): void
    {
        Video::create($this->payload(['slug' => 'publiee', 'title' => 'Capsule publiée', 'provider_id' => 'dQw4w9WgXcQ', 'published_at' => now()->subDay()]));
        Video::create($this->payload(['slug' => 'brouillon', 'title' => 'Capsule brouillon', 'provider_id' => 'dQw4w9WgXcQ', 'status' => Video::STATUS_DRAFT]));

        $this->actingAs($this->admin)
            ->get('/admin/web-tv?statut=draft')
            ->assertOk()
            ->assertSee('Capsule brouillon')
            ->assertDontSee('Capsule publiée');

        $this->actingAs($this->admin)
            ->get('/admin/web-tv?q=publi')
            ->assertOk()
            ->assertSee('Capsule publiée')
            ->assertDontSee('Capsule brouillon');
    }
}
