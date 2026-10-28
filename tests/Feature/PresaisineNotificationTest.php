<?php

namespace Tests\Feature;

use App\Mail\PresaisineReceipt;
use App\Mail\PresaisineReceived;
use App\Models\LegalDomain;
use App\Models\Registre\PresaisineSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\RefreshesRegistreDatabase;
use Tests\TestCase;

/**
 * The two messages a pré-saisine sends: an acknowledgement to the person
 * who left it, when they gave an address, and a notice to the Clinic.
 */
class PresaisineNotificationTest extends TestCase
{
    use RefreshDatabase, RefreshesRegistreDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        Storage::fake('local');

        LegalDomain::create([
            'name' => 'Droit du travail',
            'slug' => 'droit-du-travail',
            'description' => 'Contrats et salaires.',
            'is_active' => true,
            'position' => 1,
        ]);
    }

    /** @param array<string, mixed> $overrides */
    private function submit(array $overrides = []): void
    {
        $this->post('/pre-saisine', [
            'reponses' => ['urgence' => 'non', 'avocat' => 'non', 'domaine' => 'oui', 'deplacement' => 'oui'],
        ]);

        $this->post('/pre-saisine/formulaire', array_merge([
            'last_name' => 'Abdou',
            'first_name' => 'Hadiza',
            'phone' => '+227 90 11 22 33',
            'email' => 'hadiza@example.test',
            'legal_domain' => 'Droit du travail',
            'subject' => 'Salaires non versés',
            'consent' => '1',
        ], $overrides));

        $this->post('/pre-saisine/envoi');
    }

    public function test_the_applicant_receives_an_acknowledgement(): void
    {
        $this->submit();

        Mail::assertSent(PresaisineReceipt::class, function (PresaisineReceipt $mail) {
            return $mail->hasTo('hadiza@example.test');
        });
    }

    /** "si une adresse est fournie" — and only then. */
    public function test_no_acknowledgement_is_sent_without_an_address(): void
    {
        $this->submit(['email' => null]);

        Mail::assertNotSent(PresaisineReceipt::class);
        // The Clinic is told either way.
        Mail::assertSent(PresaisineReceived::class);
    }

    public function test_the_clinic_is_told_about_every_request(): void
    {
        $this->submit();

        Mail::assertSent(PresaisineReceived::class, function (PresaisineReceived $mail) {
            return $mail->hasTo(config('clinique.contact.email'));
        });
    }

    /** The reference is what the applicant is asked to quote. */
    public function test_the_acknowledgement_carries_the_reference(): void
    {
        $this->submit();

        $reference = PresaisineSubmission::first()->reference;

        Mail::assertSent(PresaisineReceipt::class, function (PresaisineReceipt $mail) use ($reference) {
            $rendered = $mail->render();

            return str_contains($mail->envelope()->subject, $reference)
                && str_contains($rendered, $reference);
        });
    }

    /**
     * An e-mail travels unencrypted into an inbox the Clinic does not
     * control, so neither message repeats what the person wrote.
     */
    public function test_neither_message_repeats_the_account_of_the_situation(): void
    {
        $this->submit(['narrative' => "Mon employeur me doit trois mois de salaire."]);

        foreach ([PresaisineReceipt::class, PresaisineReceived::class] as $mailable) {
            Mail::assertSent($mailable, function ($mail) {
                return ! str_contains($mail->render(), 'trois mois de salaire');
            });
        }
    }

    /**
     * A mail server that is down must not cost someone the request they
     * just wrote: it is stored first, and the sending cannot undo it.
     */
    public function test_a_failing_mail_server_does_not_lose_the_request(): void
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP indisponible'));

        $this->submit();

        $this->assertSame(1, PresaisineSubmission::count());
        $this->get('/pre-saisine/confirmation')
            ->assertOk()
            ->assertSee(PresaisineSubmission::first()->reference, escape: false);
    }
}
