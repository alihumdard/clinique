<?php

namespace Tests\Feature\Admin;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->category = Category::create([
            'type' => 'article',
            'name' => 'Événements',
            'slug' => 'evenements',
            'tone' => 'blue',
        ]);
    }

    public function test_listing_requires_authentication(): void
    {
        $this->get('/admin/actualites')->assertRedirect(route('admin.login'));
    }

    public function test_an_article_can_be_created(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/actualites', [
                'title' => 'Caravane juridique à Dosso',
                'category_id' => $this->category->id,
                'status' => Article::STATUS_PUBLISHED,
            ])
            ->assertRedirect(route('admin.news'))
            ->assertSessionHas('status');

        $article = Article::firstWhere('title', 'Caravane juridique à Dosso');

        $this->assertNotNull($article);
        $this->assertSame('caravane-juridique-a-dosso', $article->slug);
        $this->assertSame($this->admin->id, $article->author_id);
        $this->assertNotNull($article->published_at);
    }

    public function test_a_title_is_required(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/actualites', ['title' => '', 'status' => Article::STATUS_PUBLISHED])
            ->assertSessionHasErrors('title');

        $this->assertSame(0, Article::count());
    }

    public function test_slugs_do_not_collide(): void
    {
        $this->actingAs($this->admin);

        foreach (range(1, 3) as $ignored) {
            $this->post('/admin/actualites', [
                'title' => 'Même titre',
                'status' => Article::STATUS_DRAFT,
            ]);
        }

        $slugs = Article::pluck('slug')->all();

        $this->assertCount(3, $slugs);
        $this->assertSame($slugs, array_unique($slugs));
    }

    public function test_an_article_can_be_updated(): void
    {
        $article = Article::create([
            'title' => 'Ancien titre',
            'slug' => 'ancien-titre',
            'status' => Article::STATUS_DRAFT,
        ]);

        $this->actingAs($this->admin)
            ->put("/admin/actualites/{$article->id}", [
                'title' => 'Nouveau titre',
                'status' => Article::STATUS_PUBLISHED,
            ])
            ->assertRedirect(route('admin.news'));

        $this->assertSame('Nouveau titre', $article->fresh()->title);
    }

    public function test_deleting_moves_to_the_corbeille_and_can_be_restored(): void
    {
        $article = Article::create([
            'title' => 'À supprimer',
            'slug' => 'a-supprimer',
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->delete("/admin/actualites/{$article->id}")
            ->assertRedirect(route('admin.news'));

        $this->assertSoftDeleted($article);

        $this->actingAs($this->admin)
            ->post("/admin/actualites/{$article->id}/restaurer")
            ->assertRedirect(route('admin.news'));

        $this->assertNotSoftDeleted($article);
    }

    public function test_a_future_dated_article_is_scheduled_not_public(): void
    {
        $article = Article::create([
            'title' => 'À paraître',
            'slug' => 'a-paraitre',
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now()->addWeek(),
        ]);

        $this->assertTrue($article->isScheduled());
        $this->assertFalse($article->isPublic());
        $this->assertSame('Programmée', $article->statusLabel());

        $this->assertSame(0, Article::public()->count());
        $this->assertSame(1, Article::scheduled()->count());
    }

    public function test_an_uploaded_image_is_stored_on_the_public_disk(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post('/admin/actualites', [
            'title' => 'Avec une image',
            'status' => Article::STATUS_DRAFT,
            'image' => UploadedFile::fake()->image('photo.jpg', 800, 600),
        ]);

        $article = Article::firstWhere('title', 'Avec une image');

        $this->assertNotNull($article->image);
        Storage::disk('public')->assertExists($article->image);
    }

    public function test_a_non_image_upload_is_rejected(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)
            ->post('/admin/actualites', [
                'title' => 'Fichier piege',
                'status' => Article::STATUS_DRAFT,
                'image' => UploadedFile::fake()->create('script.php', 10, 'application/x-php'),
            ])
            ->assertSessionHasErrors('image');

        $this->assertSame(0, Article::count());
    }

    public function test_archive_years_list_only_published_articles(): void
    {
        Article::create(['title' => 'A', 'slug' => 'a', 'status' => Article::STATUS_PUBLISHED, 'published_at' => '2025-03-01']);
        Article::create(['title' => 'B', 'slug' => 'b', 'status' => Article::STATUS_PUBLISHED, 'published_at' => '2025-07-01']);
        Article::create(['title' => 'C', 'slug' => 'c', 'status' => Article::STATUS_PUBLISHED, 'published_at' => '2024-01-01']);
        Article::create(['title' => 'D', 'slug' => 'd', 'status' => Article::STATUS_DRAFT, 'published_at' => '2023-01-01']);

        $years = Article::archiveYears();

        $this->assertCount(2, $years);
        $this->assertSame(2025, (int) $years->first()->year);
        $this->assertSame(2, (int) $years->first()->total);
    }
}
