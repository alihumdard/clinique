<?php

namespace Tests\Feature;

use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The form is rate limited per address; without clearing it the
        // tests would start throttling one another.
        RateLimiter::clear('newsletter:127.0.0.1');
    }

    public function test_a_visitor_can_subscribe(): void
    {
        $this->post('/newsletter', ['email' => 'lecteur@example.ne'])
            ->assertRedirect()
            ->assertSessionHas('newsletter');

        $subscriber = NewsletterSubscriber::firstWhere('email', 'lecteur@example.ne');

        $this->assertNotNull($subscriber);
        $this->assertTrue($subscriber->isSubscribed());
        // Every subscriber gets a token, which is what makes an unsubscribe
        // link work without them signing in anywhere.
        $this->assertNotEmpty($subscriber->token);
    }

    public function test_an_invalid_address_is_refused(): void
    {
        $this->post('/newsletter', ['email' => 'pas-une-adresse'])
            ->assertSessionHasErrors('email');

        $this->assertSame(0, NewsletterSubscriber::count());
    }

    /** Subscribing twice must not create a second row. */
    public function test_subscribing_twice_reuses_the_row(): void
    {
        $this->post('/newsletter', ['email' => 'lecteur@example.ne']);
        RateLimiter::clear('newsletter:127.0.0.1');
        $this->post('/newsletter', ['email' => 'lecteur@example.ne']);

        $this->assertSame(1, NewsletterSubscriber::count());
    }

    /** A field nobody sees and a script usually fills in. */
    public function test_a_filled_honeypot_is_refused(): void
    {
        $this->post('/newsletter', [
            'email' => 'robot@example.ne',
            'website' => 'http://spam.example',
        ])->assertSessionHasErrors('website');

        $this->assertSame(0, NewsletterSubscriber::count());
    }

    public function test_the_form_is_rate_limited(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->post('/newsletter', ['email' => "lecteur{$i}@example.ne"]);
        }

        $this->post('/newsletter', ['email' => 'lecteur6@example.ne'])
            ->assertSessionHasErrors('email');

        $this->assertSame(5, NewsletterSubscriber::count());
    }

    /*
    |--------------------------------------------------------------------------
    | Unsubscribing
    |--------------------------------------------------------------------------
    */

    public function test_a_token_unsubscribes_without_signing_in(): void
    {
        $subscriber = NewsletterSubscriber::create(['email' => 'lecteur@example.ne']);

        $this->get(route('newsletter.unsubscribe', $subscriber->token))
            ->assertOk()
            ->assertSee('désinscription', escape: false);

        $this->assertFalse($subscriber->fresh()->isSubscribed());
    }

    /**
     * An unknown token gets the same page as a known one: otherwise a
     * stranger could use the difference to test whether an address is on
     * the list.
     */
    public function test_an_unknown_token_gives_nothing_away(): void
    {
        $this->get(route('newsletter.unsubscribe', 'un-token-inconnu'))
            ->assertOk()
            ->assertSee('désinscription', escape: false);
    }

    /** Leaving is recorded rather than deleted, so an import cannot undo it. */
    public function test_unsubscribing_keeps_the_row(): void
    {
        $subscriber = NewsletterSubscriber::create(['email' => 'lecteur@example.ne']);
        $subscriber->unsubscribe();

        $this->assertDatabaseHas('newsletter_subscribers', ['email' => 'lecteur@example.ne']);
        $this->assertSame(0, NewsletterSubscriber::active()->count());
    }

    /** Coming back is a new consent, so the unsubscribe is cleared. */
    public function test_re_subscribing_after_leaving_works(): void
    {
        $subscriber = NewsletterSubscriber::create(['email' => 'lecteur@example.ne']);
        $subscriber->unsubscribe();

        $this->post('/newsletter', ['email' => 'lecteur@example.ne']);

        $this->assertTrue($subscriber->fresh()->isSubscribed());
        $this->assertSame(1, NewsletterSubscriber::count());
    }

    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */

    public function test_the_admin_list_requires_authentication(): void
    {
        $this->get('/admin/newsletter')->assertRedirect(route('admin.login'));
        $this->get('/admin/newsletter/export')->assertRedirect(route('admin.login'));
    }

    public function test_the_export_is_a_csv_of_active_subscribers_only(): void
    {
        NewsletterSubscriber::create(['email' => 'actif@example.ne', 'name' => 'Lecteur Actif']);

        $gone = NewsletterSubscriber::create(['email' => 'parti@example.ne']);
        $gone->unsubscribe();

        $response = $this->actingAs(User::factory()->create())
            ->get('/admin/newsletter/export');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $csv = $response->streamedContent();

        $this->assertStringContainsString('actif@example.ne', $csv);
        // Exporting someone who left is how they get mailed again.
        $this->assertStringNotContainsString('parti@example.ne', $csv);
        $this->assertStringContainsString('Lecteur Actif', $csv);
    }

    public function test_an_admin_can_unsubscribe_someone(): void
    {
        $subscriber = NewsletterSubscriber::create(['email' => 'lecteur@example.ne']);

        $this->actingAs(User::factory()->create())
            ->post("/admin/newsletter/{$subscriber->id}/desinscrire")
            ->assertRedirect();

        $this->assertFalse($subscriber->fresh()->isSubscribed());
    }

    public function test_the_admin_list_filters_on_status(): void
    {
        NewsletterSubscriber::create(['email' => 'actif@example.ne']);
        NewsletterSubscriber::create(['email' => 'parti@example.ne'])->unsubscribe();

        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get('/admin/newsletter?statut=actifs')
            ->assertOk()
            ->assertSee('actif@example.ne')
            ->assertDontSee('parti@example.ne');

        $this->actingAs($admin)
            ->get('/admin/newsletter?statut=desinscrits')
            ->assertOk()
            ->assertSee('parti@example.ne')
            ->assertDontSee('actif@example.ne');
    }
}
