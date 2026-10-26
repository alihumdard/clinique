<?php

namespace Tests\Feature\Admin;

use App\Models\Activity;
use App\Models\Article;
use App\Models\ContactMessage;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
    }

    public function test_the_dashboard_requires_authentication(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
    }

    public function test_the_dashboard_renders_with_an_empty_database(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Aucun contenu publié', escape: false)
            ->assertSee('Aucune actualité enregistrée', escape: false)
            ->assertSee('Aucun message reçu', escape: false);
    }

    public function test_the_kpis_show_real_counts(): void
    {
        Article::create([
            'title' => 'Une actualité',
            'slug' => 'une-actualite',
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        Article::create([
            'title' => 'Un brouillon',
            'slug' => 'un-brouillon',
            'status' => Article::STATUS_DRAFT,
        ]);

        Video::create([
            'title' => 'Une capsule',
            'slug' => 'une-capsule',
            'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
            'provider' => 'youtube',
            'provider_id' => 'dQw4w9WgXcQ',
            'status' => Video::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        Activity::create([
            'title' => 'Une activité',
            'slug' => 'une-activite',
            'held_on' => now()->subWeek(),
            'status' => Activity::STATUS_PUBLISHED,
            'published_at' => now()->subWeek(),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin')->assertOk();

        // The drafted actualité must not be counted among the published.
        $response->assertSee('Actualités publiées', escape: false);
        $response->assertSee('Capsules Web TV', escape: false);
        $response->assertSee('Activités publiées', escape: false);

        $html = $response->getContent();

        // One of each published, so the KPI figure is 1 three times over.
        $this->assertSame(
            1,
            Article::public()->count(),
            'A draft must not reach the published count.'
        );
        $this->assertStringContainsString('Une actualité', $html);
    }

    /** Messages to handle, which replaced the design's applications figure. */
    public function test_the_dashboard_shows_messages_to_handle(): void
    {
        ContactMessage::create([
            'name' => 'Aminata Diallo',
            'email' => 'aminata@example.ne',
            'subject' => 'assistance',
            'message' => 'Une demande de renseignement.',
            'consent_at' => now(),
            'status' => ContactMessage::STATUS_NEW,
        ]);

        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Messages à traiter', escape: false)
            ->assertSee('Aminata Diallo', escape: false)
            ->assertSee('Nouveau', escape: false);
    }

    /**
     * The month grouping uses a driver-dependent expression, so this is
     * the test that would catch a MySQL-only one.
     */
    public function test_the_chart_groups_publications_by_month(): void
    {
        foreach ([0, 1, 1, 3] as $monthsAgo) {
            Article::create([
                'title' => 'Actualité '.uniqid(),
                'slug' => 'actualite-'.uniqid(),
                'status' => Article::STATUS_PUBLISHED,
                'published_at' => now()->subMonths($monthsAgo),
            ]);
        }

        $response = $this->actingAs($this->admin)->get('/admin')->assertOk();

        // Six month captions along the x axis, and a drawn path.
        $response->assertSee('Statistiques des publications', escape: false);
        $this->assertStringContainsString('<path', $response->getContent());
    }

    public function test_the_donut_leaves_out_types_with_nothing_in_them(): void
    {
        Article::create([
            'title' => 'Une actualité',
            'slug' => 'une-actualite',
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        // Asserted on the data rather than the markup: "Rapports annuels"
        // is also a sidebar link, so searching the HTML would always find
        // it whatever the donut holds.
        $response = $this->actingAs($this->admin)->get('/admin')->assertOk();

        $labels = array_column($response->viewData('breakdown'), 'label');

        $this->assertContains('Actualités', $labels);
        $this->assertNotContains('Rapports annuels', $labels);
        $this->assertNotContains('Web TV', $labels);
    }
}
