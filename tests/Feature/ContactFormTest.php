<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('contact:127.0.0.1');
    }

    /** @return array<string, mixed> */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Aminata Diallo',
            'email' => 'aminata@example.ne',
            'phone' => '+227 90 00 00 00',
            'subject' => 'assistance',
            'message' => "Bonjour, je souhaite obtenir des informations sur une procédure d'état civil.",
            'consent' => '1',
        ], $overrides);
    }

    public function test_a_visitor_can_send_a_message(): void
    {
        $this->post('/contact', $this->payload())
            ->assertRedirect()
            ->assertSessionHas('contact');

        $message = ContactMessage::first();

        $this->assertNotNull($message);
        $this->assertSame('Aminata Diallo', $message->name);
        $this->assertSame(ContactMessage::STATUS_NEW, $message->status);
        // The plan asks for the agreement to be recorded, with its moment.
        $this->assertNotNull($message->consent_at);
    }

    /** The agreement is required, not merely noted. */
    public function test_a_message_without_consent_is_refused(): void
    {
        $this->post('/contact', $this->payload(['consent' => null]))
            ->assertSessionHasErrors('consent');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_an_invalid_email_is_refused(): void
    {
        $this->post('/contact', $this->payload(['email' => 'pas-une-adresse']))
            ->assertSessionHasErrors('email');
    }

    /** The subject has to be one the form offers. */
    public function test_a_subject_outside_the_list_is_refused(): void
    {
        $this->post('/contact', $this->payload(['subject' => 'autre-chose']))
            ->assertSessionHasErrors('subject');
    }

    public function test_a_one_word_message_is_refused(): void
    {
        $this->post('/contact', $this->payload(['message' => 'Bonjour']))
            ->assertSessionHasErrors('message');
    }

    /** A field nobody sees and a script usually fills in. */
    public function test_a_filled_honeypot_is_refused(): void
    {
        $this->post('/contact', $this->payload(['website' => 'http://spam.example']))
            ->assertSessionHasErrors('website');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_the_form_is_rate_limited(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            $this->post('/contact', $this->payload(['email' => "visiteur{$i}@example.ne"]));
        }

        $this->post('/contact', $this->payload(['email' => 'visiteur4@example.ne']))
            ->assertSessionHasErrors('message');

        $this->assertSame(3, ContactMessage::count());
    }

    /** What was typed survives a failed submission. */
    public function test_the_form_keeps_what_was_typed_when_it_fails(): void
    {
        $this->from('/contact')
            ->post('/contact', $this->payload(['email' => 'invalide']))
            ->assertRedirect('/contact');

        $this->followingRedirects()
            ->from('/contact')
            ->post('/contact', $this->payload(['email' => 'invalide']))
            ->assertSee('Aminata Diallo', escape: false);
    }

    /*
    |--------------------------------------------------------------------------
    | The inbox
    |--------------------------------------------------------------------------
    */

    public function test_the_inbox_requires_authentication(): void
    {
        $this->get('/admin/messages')->assertRedirect(route('admin.login'));
    }

    public function test_a_message_appears_in_the_inbox(): void
    {
        $this->post('/contact', $this->payload());

        $this->actingAs(User::factory()->create())
            ->get('/admin/messages')
            ->assertOk()
            ->assertSee('Aminata Diallo', escape: false)
            ->assertSee('aminata@example.ne')
            // Default escaping, so the apostrophe is compared the way
            // Blade rendered it.
            ->assertSee("procédure d'état civil");
    }

    public function test_a_message_can_be_marked_handled(): void
    {
        $this->post('/contact', $this->payload());
        $message = ContactMessage::first();
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->post("/admin/messages/{$message->id}/traite")
            ->assertRedirect();

        $message->refresh();

        $this->assertSame(ContactMessage::STATUS_HANDLED, $message->status);
        $this->assertSame($admin->id, $message->handled_by);
        $this->assertNotNull($message->handled_at);
    }

    /** No corbeille: a deletion of personal data is permanent. */
    public function test_deleting_a_message_is_permanent(): void
    {
        $this->post('/contact', $this->payload());
        $message = ContactMessage::first();

        $this->actingAs(User::factory()->create())
            ->delete("/admin/messages/{$message->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('contact_messages', ['id' => $message->id]);
    }

    public function test_the_inbox_filters_on_status(): void
    {
        $this->post('/contact', $this->payload(['name' => 'Nouveau Message']));
        RateLimiter::clear('contact:127.0.0.1');
        $this->post('/contact', $this->payload(['name' => 'Message Traite', 'email' => 'autre@example.ne']));

        $admin = User::factory()->create();
        ContactMessage::firstWhere('name', 'Message Traite')->markHandled($admin);

        $this->actingAs($admin)
            ->get('/admin/messages?statut=nouveaux')
            ->assertOk()
            ->assertSee('Nouveau Message', escape: false)
            ->assertDontSee('Message Traite', escape: false);
    }

    /** Kept for abuse handling, and never serialised out of the app. */
    public function test_the_ip_address_is_recorded_but_hidden(): void
    {
        $this->post('/contact', $this->payload());

        $message = ContactMessage::first();

        $this->assertNotNull($message->ip_address);
        $this->assertArrayNotHasKey('ip_address', $message->toArray());
    }
}
