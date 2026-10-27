<?php

namespace Tests\Feature;

use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The FR / EN switch.
 *
 * The English comes from the site's own translation file, not from a
 * third party: Google's widget would have meant loosening the
 * Content-Security-Policy and sending every page's text — including what
 * someone typed about their own situation — to Google.
 *
 * French is both the default and the fallback, so a string nobody has
 * translated yet is served in French rather than as a missing key.
 */
class LanguageSwitcherTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_switch_offers_french_and_english(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('/langue/en', escape: false)
            ->assertSee('FR', escape: false)
            ->assertSee('EN', escape: false);
    }

    public function test_french_is_served_by_default(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Liens rapides', escape: false)
            ->assertDontSee('Quick links', escape: false);
    }

    public function test_choosing_english_translates_the_chrome(): void
    {
        $this->get('/langue/en')->assertRedirect();

        $this->withCookie(SetLocale::COOKIE, 'en')
            ->get('/')
            ->assertOk()
            ->assertSee('Quick links', escape: false)
            ->assertSee('Our team', escape: false);
    }

    public function test_choosing_french_brings_it_back(): void
    {
        $this->withCookie(SetLocale::COOKIE, 'fr')
            ->get('/')
            ->assertOk()
            ->assertSee('Liens rapides', escape: false)
            ->assertDontSee('Quick links', escape: false);
    }

    public function test_the_switch_returns_to_the_page_it_was_used_on(): void
    {
        $this->get('/langue/en', ['referer' => url('/actualites')])
            ->assertRedirect(url('/actualites'));
    }

    public function test_an_unknown_language_is_refused(): void
    {
        $this->get('/langue/de')->assertNotFound();
    }

    /** A page nobody has translated yet still reads, in French. */
    public function test_an_untranslated_string_falls_back_to_french(): void
    {
        $this->withCookie(SetLocale::COOKIE, 'en')
            ->get('/')
            ->assertOk()
            // Translated chrome…
            ->assertSee('Our services', escape: false)
            // …around French content that is served as it stands.
            ->assertSee('Nos domaines', escape: false);
    }

    /**
     * The security checklist asks for a strict policy, and nothing about
     * this feature loosens it: the English is served from this origin.
     */
    public function test_nothing_is_loaded_from_a_third_party(): void
    {
        $response = $this->withCookie(SetLocale::COOKIE, 'en')->get('/')->assertOk();

        $response->assertDontSee('translate.google.com', escape: false);
        $this->assertStringContainsString(
            "script-src 'self'",
            (string) $response->headers->get('Content-Security-Policy')
        );
    }
}
