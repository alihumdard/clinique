<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Covers the administration area: that every screen renders for a signed-in
 * administrator, and — more importantly — that none of them is reachable
 * without signing in.
 */
class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function admin(): User
    {
        return User::factory()->create([
            'email' => 'admin@example.test',
            'password' => Hash::make('correct-horse-battery-staple'),
        ]);
    }

    /**
     * Every administration screen.
     *
     * @return array<string, array{string, string}>
     */
    public static function adminRoutes(): array
    {
        return [
            'dashboard' => ['/admin', 'Bienvenue'],
            'pages' => ['/admin/pages', 'Gestion des pages'],
            'news' => ['/admin/actualites', 'Gestion des actualit'],
            'web tv' => ['/admin/web-tv', 'Gestion de la Web TV'],
            'partners' => ['/admin/partenaires', 'Gestion des partenaires'],
            'team' => ['/admin/equipe', 'Gestion de l'],
            'activities' => ['/admin/activites', 'Gestion des activit'],
            'guides' => ['/admin/fiches-pratiques', 'Gestion des fiches pratiques'],
            'reports' => ['/admin/rapports-annuels', 'Gestion des rapports annuels'],
            'settings' => ['/admin/parametres', 'Param'],
            'newsletter' => ['/admin/newsletter', 'abonnés'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('adminRoutes')]
    public function test_screen_renders_for_authenticated_admin(string $uri, string $expected): void
    {
        $response = $this->actingAs($this->admin())->get($uri);

        $response->assertOk();
        $response->assertSee($expected, escape: false);
    }

    /**
     * The security requirement that matters most: an unauthenticated
     * visitor must never reach an administration screen.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('adminRoutes')]
    public function test_screen_is_unreachable_when_signed_out(string $uri): void
    {
        $this->get($uri)->assertRedirect(route('admin.login'));
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('adminRoutes')]
    public function test_screen_has_no_unresolved_icons(string $uri): void
    {
        $html = $this->actingAs($this->admin())->get($uri)->getContent();

        $this->assertStringNotContainsString('<path d="" />', $html);
    }

    /** The admin area must never be indexed by search engines. */
    #[\PHPUnit\Framework\Attributes\DataProvider('adminRoutes')]
    public function test_screen_is_noindex(string $uri): void
    {
        $this->actingAs($this->admin())
            ->get($uri)
            ->assertSee('noindex, nofollow', escape: false);
    }

    public function test_login_page_is_reachable_and_noindex(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('Connexion Administrateur', escape: false)
            ->assertSee('noindex, nofollow', escape: false);
    }

    public function test_valid_credentials_sign_the_admin_in(): void
    {
        $admin = $this->admin();

        $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'correct-horse-battery-staple',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_wrong_password_is_rejected(): void
    {
        $admin = $this->admin();

        $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'not-the-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    /**
     * The error must not reveal whether the address belongs to an account,
     * otherwise it becomes an administrator-enumeration oracle.
     */
    public function test_login_error_does_not_disclose_whether_the_account_exists(): void
    {
        $admin = $this->admin();

        $known = $this->post('/admin/login', ['email' => $admin->email, 'password' => 'wrong'])
            ->getSession()->get('errors')->first('email');

        $this->flushSession();

        $unknown = $this->post('/admin/login', ['email' => 'nobody@example.test', 'password' => 'wrong'])
            ->getSession()->get('errors')->first('email');

        $this->assertSame($known, $unknown);
    }

    public function test_repeated_failures_are_throttled(): void
    {
        $admin = $this->admin();

        // The limiter allows five attempts before locking the pair out.
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post('/admin/login', [
                'email' => $admin->email,
                'password' => 'wrong',
            ]);
        }

        $this->assertStringContainsString(
            'Trop de tentatives',
            $response->getSession()->get('errors')->first('email')
        );
    }

    public function test_signing_out_requires_post_and_ends_the_session(): void
    {
        $this->actingAs($this->admin());

        // A GET must not end the session: state changes need POST.
        $this->get('/admin/logout')->assertStatus(405);
        $this->assertAuthenticated();

        $this->post('/admin/logout')->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_signed_in_admin_is_redirected_away_from_the_login_page(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/login')
            ->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Every GET route under /admin must render. A route pointing at a view
     * that was never built raises a 500 the moment someone clicks it in the
     * sidebar, which is how six dead links reached the server.
     */
    public function test_every_admin_get_route_renders(): void
    {
        $this->actingAs($this->admin());

        $routes = collect(\Illuminate\Support\Facades\Route::getRoutes())
            ->filter(fn ($route) => str_starts_with($route->uri(), 'admin')
                && in_array('GET', $route->methods(), true)
                && ! str_contains($route->uri(), '{'));

        $this->assertGreaterThan(10, $routes->count(), 'Expected the admin routes to be registered.');

        foreach ($routes as $route) {
            $uri = '/'.$route->uri();
            $status = $this->get($uri)->getStatusCode();

            $this->assertContains(
                $status,
                [200, 302],
                "{$uri} returned {$status}; a sidebar link that errors is a dead screen."
            );
        }
    }
}
