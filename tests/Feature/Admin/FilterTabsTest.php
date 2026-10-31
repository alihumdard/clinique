<?php

namespace Tests\Feature\Admin;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * The tabs above a listing, and the figure each one carries.
 *
 * Two faults met on the same screen. app.js called preventDefault() on
 * the tab links and tried to filter in the browser instead: it hid the
 * rows whose text did not contain a keyword taken from the tab's own
 * label. It filtered the ten rows on the page while the footer went on
 * counting the unfiltered total — "1 à 10 sur 14" above an empty table —
 * and the keywords were French, so with the interface in English nothing
 * matched and every tab emptied the listing.
 *
 * And the counts were queried with public() while the listing filters on
 * the date alone, so "Passées (11)" opened twelve rows: the twelfth a
 * draft, which this screen is exactly where you go to see.
 *
 * A tab's figure has to be the number of rows the tab opens.
 */
class FilterTabsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    private function activity(string $title, string $heldOn, string $status = Activity::STATUS_PUBLISHED): Activity
    {
        return Activity::create([
            'title' => $title,
            'slug' => Str::slug($title),
            'held_on' => $heldOn,
            'status' => $status,
            'published_at' => now()->subMonth(),
        ]);
    }

    public function test_a_tab_opens_the_rows_it_counts(): void
    {
        $this->activity('Caravane passée', now()->subMonth()->toDateString());
        $this->activity('Atelier interne', now()->subWeek()->toDateString(), Activity::STATUS_DRAFT);
        $this->activity('Caravane à venir', now()->addMonth()->toDateString());

        $response = $this->actingAs($this->admin())
            ->get(route('admin.activities', ['vue' => 'passees']))
            ->assertOk();

        // Two are in the past: one published, one still a draft. The draft
        // belongs on this screen, so the tab has to count it.
        $response->assertSee('Passées (2)', escape: false);
        $response->assertSee('Caravane passée', escape: false);
        $response->assertSee('Atelier interne', escape: false);
        $response->assertDontSee('Caravane à venir', escape: false);
    }

    public function test_the_upcoming_tab_counts_what_it_opens(): void
    {
        $this->activity('Caravane passée', now()->subMonth()->toDateString());
        $this->activity('Caravane à venir', now()->addMonth()->toDateString());
        $this->activity('Atelier à préparer', now()->addWeek()->toDateString(), Activity::STATUS_DRAFT);

        $response = $this->actingAs($this->admin())
            ->get(route('admin.activities', ['vue' => 'a-venir']))
            ->assertOk();

        $response->assertSee('À venir (2)', escape: false);
        $response->assertDontSee('Caravane passée', escape: false);
    }

    public function test_the_cards_above_keep_counting_what_the_site_shows(): void
    {
        $this->activity('Caravane passée', now()->subMonth()->toDateString());
        $this->activity('Atelier interne', now()->subWeek()->toDateString(), Activity::STATUS_DRAFT);

        $html = $this->actingAs($this->admin())
            ->get(route('admin.activities'))
            ->assertOk()
            ->getContent();

        // The stat card answers a different question from the tab: what a
        // visitor sees, not what this screen lists. One of the two past
        // activities is a draft, so the card says one.
        $this->assertStringContainsString('Activités passées', $html);
        $this->assertStringContainsString('Passées (2)', $html);
    }

    public function test_the_tabs_are_real_links_so_the_filter_reaches_the_server(): void
    {
        $this->activity('Caravane passée', now()->subMonth()->toDateString());

        $html = $this->actingAs($this->admin())
            ->get(route('admin.activities'))
            ->assertOk()
            ->getContent();

        // Filtering in the browser could only ever act on the rows of the
        // current page, and left the footer's total untouched.
        $this->assertStringContainsString(
            route('admin.activities', ['vue' => 'passees']),
            $html
        );
    }
}
