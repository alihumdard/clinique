<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
    }

    /** @return array<string, mixed> */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Accessibilité',
            'slug' => 'accessibilite',
            'body' => "Notre engagement pour un site accessible à tous.",
            'status' => Page::STATUS_PUBLISHED,
        ], $overrides);
    }

    public function test_the_screen_requires_authentication(): void
    {
        $this->get('/admin/pages')->assertRedirect(route('admin.login'));
    }

    public function test_a_page_is_created_and_served_at_its_slug(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/pages', $this->payload())
            ->assertRedirect(route('admin.pages'));

        $this->get('/accessibilite')
            ->assertOk()
            ->assertSee('Accessibilité', escape: false)
            ->assertSee('engagement pour un site accessible', escape: false);
    }

    public function test_a_draft_page_is_not_public(): void
    {
        Page::create($this->payload([
            'slug' => 'brouillon',
            'status' => Page::STATUS_DRAFT,
        ]));

        $this->get('/brouillon')->assertNotFound();
    }

    /**
     * The catch-all is registered after every other route, so a fixed path
     * still reaches its own controller rather than being looked up as a
     * page. /admin in particular would otherwise 404.
     */
    public function test_the_catch_all_does_not_swallow_the_sites_own_routes(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));

        $this->actingAs($this->admin)->get('/admin')->assertOk();

        foreach (['/contact', '/services', '/partenaires', '/recherche'] as $uri) {
            $this->get($uri)->assertOk();
        }
    }

    /** A slug the site already owns would never be reached. */
    public function test_a_reserved_slug_is_refused(): void
    {
        foreach (['contact', 'services', 'admin', 'actualites'] as $slug) {
            $this->actingAs($this->admin)
                ->post('/admin/pages', $this->payload(['slug' => $slug]))
                ->assertSessionHasErrors('slug');
        }

        $this->assertSame(0, Page::count());
    }

    public function test_a_duplicate_slug_is_refused(): void
    {
        Page::create($this->payload());

        $this->actingAs($this->admin)
            ->post('/admin/pages', $this->payload(['title' => 'Autre page']))
            ->assertSessionHasErrors('slug');
    }

    public function test_a_slug_with_a_slash_is_refused(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/pages', $this->payload(['slug' => 'infos/accessibilite']))
            ->assertSessionHasErrors('slug');
    }

    /** The body is escaped, so a page cannot put script on the site. */
    public function test_the_body_cannot_inject_markup(): void
    {
        Page::create($this->payload([
            'slug' => 'test-xss',
            'body' => '<script>alert(1)</script>',
        ]));

        $html = $this->get('/test-xss')->assertOk()->getContent();

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    public function test_the_seo_fields_are_used_when_present(): void
    {
        Page::create($this->payload([
            'slug' => 'credits',
            'title' => 'Crédits',
            'seo_title' => 'Crédits du site',
            'seo_description' => 'Qui a conçu ce site.',
        ]));

        $this->get('/credits')
            ->assertOk()
            ->assertSee('Crédits du site', escape: false)
            ->assertSee('Qui a conçu ce site.', escape: false);
    }

    public function test_a_page_goes_to_the_corbeille_and_can_be_restored(): void
    {
        $page = Page::create($this->payload());

        $this->actingAs($this->admin)->delete("/admin/pages/{$page->id}");
        $this->assertSoftDeleted($page);
        // Trashed means gone from the site too.
        $this->get('/accessibilite')->assertNotFound();

        $this->actingAs($this->admin)->post("/admin/pages/{$page->id}/restaurer");
        $this->assertNotSoftDeleted($page->fresh());
        $this->get('/accessibilite')->assertOk();
    }

    /** The site's own pages are listed for reference, not as editable rows. */
    public function test_the_screen_lists_the_fixed_pages_for_reference(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/pages')
            ->assertOk()
            ->assertSee('Pages principales du site', escape: false)
            ->assertSee('Mentions légales', escape: false);
    }

    public function test_an_unknown_address_is_still_a_404(): void
    {
        $this->get('/cette-page-nexiste-pas')->assertNotFound();
    }
}
