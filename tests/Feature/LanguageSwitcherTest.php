<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The FR / EN switch.
 *
 * English is produced by Google Translate, which is a remote script — and
 * the performance plan forbids those. The resolution is that the script
 * is only loaded once a visitor has asked for English: a French visitor
 * loads none, which is what these tests hold the implementation to.
 */
class LanguageSwitcherTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_switch_offers_french_and_english(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('data-language="fr"', escape: false)
            ->assertSee('data-language="en"', escape: false);
    }

    /** The default page must not reach out to Google at all. */
    public function test_nothing_is_loaded_from_google_by_default(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('translate.google.com', escape: false);
    }

    public function test_the_script_is_loaded_once_english_is_chosen(): void
    {
        $this->withUnencryptedCookie('googtrans', '/fr/en')
            ->get('/')
            ->assertOk()
            ->assertSee('translate.google.com/translate_a/element.js', escape: false);
    }

    public function test_clearing_the_cookie_returns_the_page_to_french(): void
    {
        $this->withUnencryptedCookie('googtrans', '')
            ->get('/')
            ->assertOk()
            ->assertDontSee('translate.google.com', escape: false);
    }

    /** The switch itself must not be translated, or FR/EN become FR/FR. */
    public function test_the_switch_is_excluded_from_translation(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('notranslate flex items-center gap-1', escape: false);
    }

    /**
     * What someone typed about their own situation is not sent to Google:
     * translating a page sends its text, and this is a legal clinic.
     */
    public function test_what_the_visitor_typed_is_never_translated(): void
    {
        $this->post('/pre-saisine', [
            'reponses' => ['urgence' => 'non', 'avocat' => 'non', 'domaine' => 'oui', 'deplacement' => 'oui'],
        ]);

        $this->post('/pre-saisine/formulaire', [
            'last_name' => 'Abdou',
            'phone' => '+227 90 11 22 33',
            'legal_domain' => 'Droit du travail',
            'subject' => 'Salaires non versés',
            'narrative' => "Mon employeur ne m'a pas payé depuis trois mois.",
            'consent' => '1',
        ]);

        $response = $this->get('/pre-saisine/verification')->assertOk();

        $html = $response->getContent();
        // Without the apostrophe: Blade escapes it, so the raw sentence
        // is not what lands in the HTML.
        $position = mb_strpos($html, 'pas payé depuis trois mois');

        $this->assertNotFalse($position, 'the narrative should be shown back for review');

        // The element carrying it declares translate="no".
        $block = mb_substr($html, max(0, $position - 400), 400);
        $this->assertStringContainsString('translate="no"', $block);
    }
}
