<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Taking an image away again.
 *
 * Every upload field could replace a picture and none could remove one:
 * "Choose File" was the only control, so a cover uploaded by mistake
 * stayed on the public page for good and the only way out was to put a
 * second picture over it. The edit forms now carry a "Retirer l'image"
 * checkbox, and ManagesContent::applyImage() is what reads it.
 *
 * The three cases that matter are a replacement, a removal, and neither
 * — the last one being the easiest to get wrong, since a form that sends
 * no file must leave the stored image exactly where it is rather than
 * writing a null over it.
 */
class ImageRemovalTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Category $rubrique;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

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

    private function videoWithCover(): Video
    {
        $this->actingAs($this->admin)->post('/admin/web-tv', $this->payload([
            'cover' => UploadedFile::fake()->image('vignette.jpg', 640, 360),
        ]));

        $video = Video::firstOrFail();
        $this->assertNotNull($video->cover, 'the fixture needs a stored cover to remove');

        return $video;
    }

    public function test_ticking_the_box_clears_the_column_and_deletes_the_file(): void
    {
        $video = $this->videoWithCover();
        $stored = $video->cover;

        $this->actingAs($this->admin)
            ->put('/admin/web-tv/'.$video->id, $this->payload(['remove_cover' => '1']))
            ->assertSessionHasNoErrors();

        $this->assertNull($video->fresh()->cover);

        // The row and the file go together: a column cleared while the
        // file stays behind fills the disk with pictures nothing points at.
        Storage::disk('public')->assertMissing($stored);
    }

    public function test_sending_no_file_and_no_tick_leaves_the_image_alone(): void
    {
        $video = $this->videoWithCover();
        $stored = $video->cover;

        // The common edit: a title changed, the picture untouched. An
        // absent file input must not be read as "remove it".
        $this->actingAs($this->admin)
            ->put('/admin/web-tv/'.$video->id, $this->payload(['title' => 'Un autre titre']))
            ->assertSessionHasNoErrors();

        $this->assertSame($stored, $video->fresh()->cover);
        Storage::disk('public')->assertExists($stored);
    }

    public function test_a_new_file_replaces_the_old_one_and_drops_it(): void
    {
        $video = $this->videoWithCover();
        $first = $video->cover;

        $this->actingAs($this->admin)->put('/admin/web-tv/'.$video->id, $this->payload([
            'cover' => UploadedFile::fake()->image('autre.jpg', 640, 360),
        ]));

        $second = $video->fresh()->cover;

        $this->assertNotNull($second);
        $this->assertNotSame($first, $second);
        Storage::disk('public')->assertExists($second);
        Storage::disk('public')->assertMissing($first);
    }

    public function test_a_file_wins_over_the_tick_so_an_upload_is_never_lost(): void
    {
        $video = $this->videoWithCover();

        // Both sent at once — a tick left over from an earlier glance at
        // the form, then a picture chosen after all. The picture is the
        // later intention and the one that would otherwise be thrown away.
        $this->actingAs($this->admin)->put('/admin/web-tv/'.$video->id, $this->payload([
            'cover' => UploadedFile::fake()->image('choisie.jpg', 640, 360),
            'remove_cover' => '1',
        ]));

        $this->assertNotNull($video->fresh()->cover);
    }

    public function test_the_box_is_offered_only_on_a_row_that_has_an_image(): void
    {
        $this->videoWithCover();

        Video::create($this->payload([
            'title' => 'Sans vignette',
            'slug' => 'sans-vignette',
            'provider' => 'youtube',
            'video_id' => 'abc123',
        ]));

        $html = $this->actingAs($this->admin)->get('/admin/web-tv')->assertOk()->getContent();

        // One dialog offers it, the other does not: a control that would
        // remove nothing is not drawn at all, as everywhere else here.
        $this->assertSame(1, substr_count($html, 'name="remove_cover"'));
    }

    public function test_another_content_type_removes_its_image_the_same_way(): void
    {
        // The helper is shared, so one more type is enough to show the
        // wiring reaches past the screen it was written on.
        $this->actingAs($this->admin)->post('/admin/equipe', [
            'name' => 'Aminata Diallo',
            'role' => 'Encadrante',
            'department' => 'enseignants',
            'status' => TeamMember::STATUS_PUBLISHED,
            'photo' => UploadedFile::fake()->image('portrait.jpg', 400, 400),
        ]);

        $member = TeamMember::firstOrFail();
        $stored = $member->photo;
        $this->assertNotNull($stored);

        $this->actingAs($this->admin)->put('/admin/equipe/'.$member->id, [
            'name' => 'Aminata Diallo',
            'role' => 'Encadrante',
            'department' => 'enseignants',
            'status' => TeamMember::STATUS_PUBLISHED,
            'remove_photo' => '1',
        ]);

        $this->assertNull($member->fresh()->photo);
        Storage::disk('public')->assertMissing($stored);
    }
}
