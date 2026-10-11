<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Smoke coverage for the public site. Guards against a route, view or
 * shared component change silently breaking a page.
 */
class PublicPagesTest extends TestCase
{
    /**
     * Every public route from the Phase 2 page inventory.
     *
     * @return array<string, array{string, string}>
     */
    public static function publicRoutes(): array
    {
        return [
            'accueil' => ['/', 'Clinique Juridique'],
            'a propos' => ['/a-propos', 'propos'],
            'equipe' => ['/a-propos/equipe', 'quipe'],
            'partenaires' => ['/partenaires', 'partenaires'],
            'services' => ['/services', 'services'],
            'processus' => ['/services/processus-de-consultation', 'consultation'],
            'conditions' => ['/services/conditions-acces-gratuite', 'gratuit'],
            'tarifs' => ['/services/tarifs-administratifs', 'Tarifs'],
            'actualites' => ['/actualites', 'Actualit'],
            'actualite detail' => ['/actualites/conference-acces-droit-personnes-vulnerables', 'rence'],
            'activites' => ['/activites', 'caravanes'],
            'galerie' => ['/galerie', 'Galerie'],
            'fiches pratiques' => ['/ressources/fiches-pratiques', 'Fiches pratiques'],
            'fiche detail' => ['/ressources/fiches-pratiques/droits-de-la-famille', 'famille'],
            'rapports' => ['/ressources/rapports-annuels', 'Rapports annuels'],
            'web tv' => ['/web-tv', 'Web TV'],
            'web tv detail' => ['/web-tv/caravane-juridique-tillaberi', 'Caravane'],
            'rejoindre' => ['/rejoindre', 'candidatures'],
            'contact' => ['/contact', 'Contactez-nous'],
            'mentions legales' => ['/mentions-legales', 'Mentions'],
            'conditions utilisation' => ['/conditions-utilisation', 'Conditions'],
            'confidentialite' => ['/politique-confidentialite', 'confidentialit'],
            'recherche' => ['/recherche', 'recherche'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('publicRoutes')]
    public function test_public_page_renders(string $uri, string $expected): void
    {
        $response = $this->get($uri);

        $response->assertOk();
        $response->assertSee($expected, escape: false);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('publicRoutes')]
    public function test_public_page_has_no_unresolved_icons(string $uri): void
    {
        $html = $this->get($uri)->getContent();

        // An icon name missing from the x-icon map renders an empty path.
        $this->assertStringNotContainsString('<path d="" />', $html);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('publicRoutes')]
    public function test_public_page_has_exactly_one_h1(string $uri): void
    {
        $html = $this->get($uri)->getContent();

        $this->assertSame(
            1,
            substr_count($html, '<h1'),
            "{$uri} must have exactly one <h1> for accessibility and SEO."
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('publicRoutes')]
    public function test_public_page_does_not_leak_php(string $uri): void
    {
        $html = $this->get($uri)->getContent();

        // An escaped double quote inside a Blade attribute breaks parsing
        // and spills raw PHP into the rendered output.
        $this->assertStringNotContainsString("=> '", $html);
        $this->assertStringNotContainsString('\\"', $html);
    }

    /**
     * The performance plan forbids loading a video player before the
     * visitor presses play (~1 Mo per idle embed).
     */
    public function test_video_pages_load_no_iframe_before_click(): void
    {
        foreach (['/web-tv', '/web-tv/caravane-juridique-tillaberi'] as $uri) {
            $this->assertStringNotContainsString(
                '<iframe',
                $this->get($uri)->getContent(),
                "{$uri} must not embed a player before the visitor clicks."
            );
        }
    }

    public function test_unknown_url_returns_404(): void
    {
        $this->get('/cette-page-nexiste-pas')->assertNotFound();
    }

    public function test_security_headers_are_present(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertNotEmpty($response->headers->get('Content-Security-Policy'));
    }
}
