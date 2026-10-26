<?php

namespace Tests\Feature\Admin;

use App\Models\Activity;
use App\Models\ActivityPhoto;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ActivityCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Category $type;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->type = Category::create([
            'type' => 'activity',
            'name' => 'Caravanes juridiques',
            'slug' => 'caravanes-juridiques',
            'tone' => 'purple',
        ]);
    }

    /** @return array<string, mixed> */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Caravane juridique à Tillabéri',
            'excerpt' => 'Une journée de conseil juridique.',
            'location' => 'Tillabéri',
            'held_on' => '2025-09-28',
            'category_id' => $this->type->id,
            'status' => Activity::STATUS_PUBLISHED,
        ], $overrides);
    }

    public function test_the_listing_requires_authentication(): void
    {
        $this->get('/admin/activites')->assertRedirect(route('admin.login'));
    }

    public function test_an_activity_is_created_with_its_place_and_date(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/activites', $this->payload())
            ->assertRedirect(route('admin.activities'));

        $activity = Activity::firstWhere('slug', 'caravane-juridique-a-tillaberi');

        $this->assertNotNull($activity);
        $this->assertSame('Tillabéri', $activity->location);
        $this->assertSame('2025-09-28', $activity->held_on->toDateString());
        $this->assertNotNull($activity->published_at);
    }

    public function test_a_gallery_is_uploaded_with_the_activity(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post('/admin/activites', $this->payload([
            'photos' => [
                UploadedFile::fake()->image('une.jpg', 800, 600),
                UploadedFile::fake()->image('deux.jpg', 800, 600),
                UploadedFile::fake()->image('trois.jpg', 800, 600),
            ],
        ]));

        $activity = Activity::with('photos')->first();

        $this->assertCount(3, $activity->photos);
        // Positions are sequential, so ordering is a field rather than
        // whatever order the files happened to arrive in.
        $this->assertSame([1, 2, 3], $activity->photos->pluck('position')->all());

        foreach ($activity->photos as $photo) {
            Storage::disk('public')->assertExists($photo->path);
        }
    }

    public function test_more_photos_are_appended_after_the_existing_ones(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post('/admin/activites', $this->payload([
            'photos' => [UploadedFile::fake()->image('une.jpg')],
        ]));

        $activity = Activity::first();

        $this->actingAs($this->admin)->put("/admin/activites/{$activity->id}", $this->payload([
            'photos' => [UploadedFile::fake()->image('deux.jpg')],
        ]));

        $this->assertSame([1, 2], $activity->photos()->pluck('position')->all());
    }

    public function test_a_file_that_is_not_an_image_is_refused_from_the_gallery(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)
            ->post('/admin/activites', $this->payload([
                'photos' => [UploadedFile::fake()->create('notes.txt', 10, 'text/plain')],
            ]))
            ->assertSessionHasErrors('photos.0');

        $this->assertSame(0, Activity::count());
    }

    public function test_one_photo_can_be_removed_without_touching_the_rest(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post('/admin/activites', $this->payload([
            'photos' => [
                UploadedFile::fake()->image('une.jpg'),
                UploadedFile::fake()->image('deux.jpg'),
            ],
        ]));

        $activity = Activity::with('photos')->first();
        $photo = $activity->photos->first();
        $path = $photo->path;

        $this->actingAs($this->admin)
            ->delete("/admin/activites/{$activity->id}/photos/{$photo->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('activity_photos', ['id' => $photo->id]);
        Storage::disk('public')->assertMissing($path);
        $this->assertSame(1, $activity->photos()->count());
    }

    /** A photo id from another gallery must not be deletable through this one. */
    public function test_a_photo_from_another_activity_cannot_be_removed(): void
    {
        Storage::fake('public');

        $mine = Activity::create($this->payload(['slug' => 'mienne']));
        $other = Activity::create($this->payload(['slug' => 'autre', 'title' => 'Autre activité']));

        $photo = $other->photos()->create(['path' => 'images/activites/galerie/x.jpg', 'position' => 1]);

        $this->actingAs($this->admin)
            ->delete("/admin/activites/{$mine->id}/photos/{$photo->id}")
            ->assertNotFound();

        $this->assertDatabaseHas('activity_photos', ['id' => $photo->id]);
    }

    public function test_the_upcoming_and_past_views_split_on_the_event_date(): void
    {
        Activity::create($this->payload([
            'slug' => 'a-venir',
            'title' => 'Caravane à venir',
            'held_on' => now()->addMonth()->toDateString(),
            'published_at' => now()->subDay(),
        ]));

        Activity::create($this->payload([
            'slug' => 'passee',
            'title' => 'Caravane passée',
            'held_on' => now()->subMonth()->toDateString(),
            'published_at' => now()->subMonth(),
        ]));

        $this->actingAs($this->admin)
            ->get('/admin/activites?vue=a-venir')
            ->assertOk()
            ->assertSee('Caravane à venir', escape: false)
            ->assertDontSee('Caravane passée', escape: false);

        $this->actingAs($this->admin)
            ->get('/admin/activites?vue=passees')
            ->assertOk()
            ->assertSee('Caravane passée', escape: false)
            ->assertDontSee('Caravane à venir', escape: false);
    }

    public function test_an_activity_goes_to_the_corbeille_and_can_be_restored(): void
    {
        $activity = Activity::create($this->payload(['slug' => 'caravane']));

        $this->actingAs($this->admin)->delete("/admin/activites/{$activity->id}");
        $this->assertSoftDeleted($activity);

        $this->actingAs($this->admin)->post("/admin/activites/{$activity->id}/restaurer");
        $this->assertNotSoftDeleted($activity->fresh());
    }

    public function test_a_permanent_delete_removes_the_activity_its_photos_and_their_files(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post('/admin/activites', $this->payload([
            'photos' => [UploadedFile::fake()->image('une.jpg')],
        ]));

        $activity = Activity::with('photos')->first();
        $path = $activity->photos->first()->path;
        $activity->delete();

        $this->actingAs($this->admin)->delete("/admin/activites/{$activity->id}/definitif");

        $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
        $this->assertSame(0, ActivityPhoto::where('activity_id', $activity->id)->count());
        Storage::disk('public')->assertMissing($path);
    }
}
