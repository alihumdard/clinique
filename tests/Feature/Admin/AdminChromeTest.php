<?php

namespace Tests\Feature\Admin;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The administration's own furniture: the sign-in page, the topbar's search
 * box and its alert badge.
 *
 * All three were reported in the browser recette, and all three were cases
 * of a control that looked like it worked. The sign-in form could not be
 * reached below 1024 px, the search box had no action and submitted to
 * whatever page you were on, and the badge counted to three out of thin
 * air.
 */
class AdminChromeTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    private function message(string $status): ContactMessage
    {
        return ContactMessage::create([
            'name' => 'Aminata Diallo',
            'email' => 'aminata+'.uniqid().'@example.ne',
            'subject' => 'assistance',
            'message' => 'Une demande de renseignement.',
            'consent_at' => now(),
            'status' => $status,
        ]);
    }

    private function article(string $title): \App\Models\Article
    {
        return \App\Models\Article::create([
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title),
            'status' => \App\Models\Article::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);
    }

    /*
    |--------------------------------------------------------------------
    | The sign-in page has to be usable on a telephone
    |--------------------------------------------------------------------
    */

    public function test_the_sign_in_page_lets_the_document_scroll_on_a_narrow_screen(): void
    {
        $html = $this->get(route('admin.login'))->assertOk()->getContent();

        preg_match('~<body class="([^"]*)"~', $html, $body);
        $this->assertNotEmpty($body, 'The sign-in page has no body class to check.');

        $classes = preg_split('~\s+~', trim($body[1]));

        // Below lg the two panels stack and the form sits beneath a
        // full-height brand panel. Pinning the document to the viewport
        // height and hiding the overflow put the password field and the
        // submit button out of reach on every viewport under 1024 px, on
        // any device, with no way to scroll to them.
        $this->assertNotContains('overflow-hidden', $classes,
            'The sign-in body hides its overflow at every width, so a narrow viewport cannot scroll to the form.');
        $this->assertNotContains('h-full', $classes,
            'The sign-in body is pinned to the viewport height at every width.');
        $this->assertContains('min-h-full', $classes,
            'The sign-in body should fill the viewport without being capped by it.');
    }

    public function test_the_sign_in_form_panel_is_only_a_scrolling_column_beside_the_brand_panel(): void
    {
        $html = $this->get(route('admin.login'))->assertOk()->getContent();

        // flex-1 with its own scrollbar is right side by side and wrong
        // stacked: beneath the brand panel it is squeezed to nothing.
        $this->assertStringNotContainsString('flex flex-1 flex-col overflow-y-auto bg-slate-50', $html);
        $this->assertStringContainsString('lg:flex-1 lg:overflow-y-auto', $html);
    }

    /*
    |--------------------------------------------------------------------
    | The search box
    |--------------------------------------------------------------------
    */

    /** @return array<string, array{string}> */
    public static function searchingScreens(): array
    {
        return [
            'actualités' => ['admin.news'],
            'web tv' => ['admin.webtv'],
            'pages' => ['admin.pages'],
            'appels' => ['admin.calls'],
            'activités' => ['admin.activities'],
            'équipe' => ['admin.team'],
            'partenaires' => ['admin.partners'],
            'fiches pratiques' => ['admin.guides'],
            'rapports annuels' => ['admin.reports'],
            'messages' => ['admin.messages'],
            'newsletter' => ['admin.newsletter'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('searchingScreens')]
    public function test_a_screen_that_filters_declares_where_its_search_goes(string $route): void
    {
        $html = $this->actingAs($this->admin())->get(route($route))->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '~<form[^>]*role="search"[^>]*action="[^"]+"~',
            $html,
            "{$route} renders a search box with no action, which submits to the current URL."
        );
    }

    /** @return array<string, array{string}> */
    public static function screensWithoutSearch(): array
    {
        return [
            'tableau de bord' => ['admin.dashboard'],
            'paramètres' => ['admin.settings'],
            'référentiels' => ['admin.references'],
            'comptes' => ['admin.users'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('screensWithoutSearch')]
    public function test_a_screen_that_cannot_search_shows_no_search_box(string $route): void
    {
        $html = $this->actingAs($this->admin())->get(route($route))->assertOk()->getContent();

        // The dashboard is where signing in lands you, so its search box
        // was the first one anyone tried — and its controller never reads
        // `q`. A box that does nothing is worse than no box.
        $this->assertStringNotContainsString('id="admin-search"', $html,
            "{$route} shows a search box, but its controller does not filter on q.");
    }

    public function test_searching_a_listing_actually_narrows_it(): void
    {
        $this->article('Caravane juridique a Dosso');
        $this->article('Permanence a Niamey');

        $this->actingAs($this->admin())
            ->get(route('admin.news', ['q' => 'Caravane']))
            ->assertOk()
            ->assertSee('Caravane juridique a Dosso')
            ->assertDontSee('Permanence a Niamey');
    }

    /*
    |--------------------------------------------------------------------
    | The alert badge
    |--------------------------------------------------------------------
    */

    public function test_no_badge_is_shown_when_there_is_nothing_to_report(): void
    {
        $html = $this->actingAs($this->admin())->get(route('admin.dashboard'))->assertOk()->getContent();

        // It used to read a hard-coded 3 and link to the page you were on.
        $this->assertStringNotContainsString('notifications non lues', $html);
        $this->assertStringNotContainsString('messages non traités', $html);
    }

    public function test_the_badge_counts_unhandled_messages_and_links_to_them(): void
    {
        $this->message(ContactMessage::STATUS_NEW);
        $this->message(ContactMessage::STATUS_NEW);
        $this->message(ContactMessage::STATUS_HANDLED);

        $response = $this->actingAs($this->admin())->get(route('admin.dashboard'))->assertOk();

        $response->assertSee('2 messages non traités');
        $response->assertSee(route('admin.messages'));
    }

    public function test_the_badge_is_singular_for_one_message(): void
    {
        $this->message(ContactMessage::STATUS_NEW);

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertSee('1 message non traité');
    }

    public function test_an_editeur_is_shown_no_badge_at_all(): void
    {
        $this->message(ContactMessage::STATUS_NEW);
        $this->message(ContactMessage::STATUS_NEW);

        // An éditeur is refused /admin/messages, so a badge pointing there
        // would hand them a 403.
        $this->actingAs(User::factory()->editor()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('messages non traités');
    }

    /*
    |--------------------------------------------------------------------
    | The edit dialog
    |--------------------------------------------------------------------
    */

    public function test_the_edit_dialog_is_named_by_its_own_heading(): void
    {
        $html = $this->actingAs($this->admin())->get(route('admin.news'))->assertOk()->getContent();

        preg_match_all('~<dialog id="([^"]+)"([^>]*)>~', $html, $dialogs, PREG_SET_ORDER);
        $this->assertNotEmpty($dialogs, 'The actualités screen renders no dialog.');

        foreach ($dialogs as [$tag, $id, $rest]) {
            $this->assertStringContainsString('aria-labelledby="'.$id.'-title"', $rest,
                "The dialog {$id} has no accessible name, so a screen reader announces an unnamed dialog.");
            $this->assertStringContainsString('id="'.$id.'-title"', $html,
                "The dialog {$id} points at a heading id that is not in the page.");
        }
    }
}
