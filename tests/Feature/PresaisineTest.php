<?php

namespace Tests\Feature;

use App\Models\LegalDomain;
use App\Models\Registre\PresaisineSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\RefreshesRegistreDatabase;
use Tests\TestCase;

/**
 * The public pré-saisine: orientation, form, review, confirmation.
 */
class PresaisineTest extends TestCase
{
    use RefreshDatabase, RefreshesRegistreDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        LegalDomain::create([
            'name' => 'Droit du travail',
            'slug' => 'droit-du-travail',
            'description' => 'Contrats, licenciements, salaires.',
            'is_active' => true,
            'position' => 1,
        ]);
    }

    /** @return array<string, string> */
    private function passingAnswers(): array
    {
        return [
            'reponses' => [
                'urgence' => 'non',
                'avocat' => 'non',
                'domaine' => 'oui',
                'deplacement' => 'oui',
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function validForm(array $overrides = []): array
    {
        return array_merge([
            'last_name' => 'Abdou',
            'first_name' => 'Hadiza',
            'phone' => '+227 90 11 22 33',
            'email' => 'hadiza@example.test',
            'legal_domain' => 'Droit du travail',
            'subject' => 'Salaires non versés',
            'narrative' => "Trois mois sans salaire.",
            'consent' => '1',
        ], $overrides);
    }

    private function throughOrientation(): self
    {
        $this->post('/pre-saisine', $this->passingAnswers())->assertRedirect('/pre-saisine/formulaire');

        return $this;
    }

    // --------------------------------------------------- orientation

    public function test_the_orientation_page_asks_four_questions(): void
    {
        $this->get('/pre-saisine')
            ->assertOk()
            ->assertSee('Question 1 / 4', escape: false)
            ->assertSee('Question 4 / 4', escape: false);
    }

    /**
     * "Ne pas conserver les réponses sous forme de dossier juridique si
     * l'utilisateur n'est pas dirigé vers le formulaire de dépôt."
     */
    public function test_a_blocking_answer_ends_the_journey_and_stores_nothing(): void
    {
        $answers = $this->passingAnswers();
        $answers['reponses']['urgence'] = 'oui';

        $this->post('/pre-saisine', $answers)
            ->assertOk()
            ->assertSee('réponse immédiate', escape: false);

        $this->assertSame(0, PresaisineSubmission::count());
        $this->assertNull(session('presaisine.orientation'));
    }

    public function test_each_outcome_the_plan_names_is_reachable(): void
    {
        $cases = [
            ['avocat', 'ANAJJ'],
            ['domaine', 'sort de nos domaines'],
            ['deplacement', 'Niamey'],
        ];

        foreach ($cases as [$key, $expected]) {
            $answers = $this->passingAnswers();
            $answers['reponses'][$key] = $key === 'avocat' ? 'oui' : 'non';

            $this->post('/pre-saisine', $answers)
                ->assertOk()
                ->assertSee($expected, escape: false);
        }
    }

    public function test_the_orientation_must_be_answered(): void
    {
        $this->post('/pre-saisine', ['reponses' => ['urgence' => 'non']])
            ->assertSessionHasErrors('reponses.avocat');
    }

    public function test_the_form_cannot_be_reached_without_the_orientation(): void
    {
        $this->get('/pre-saisine/formulaire')->assertRedirect('/pre-saisine');
        $this->get('/pre-saisine/verification')->assertRedirect('/pre-saisine/formulaire');
    }

    // ---------------------------------------------------------- form

    public function test_the_minimum_fields_are_required(): void
    {
        $this->throughOrientation()
            ->post('/pre-saisine/formulaire', [])
            ->assertSessionHasErrors(['last_name', 'phone', 'legal_domain', 'subject', 'consent']);

        $this->assertSame(0, PresaisineSubmission::count());
    }

    /** The plan's words: nothing beyond the four is required. */
    public function test_a_request_without_e_mail_or_narrative_is_accepted(): void
    {
        $this->throughOrientation()
            ->post('/pre-saisine/formulaire', [
                'last_name' => 'Moussa',
                'phone' => '+227 90 00 00 02',
                'legal_domain' => 'Droit du travail',
                'subject' => 'Licenciement',
                'consent' => '1',
            ])
            ->assertRedirect('/pre-saisine/verification');

        $this->post('/pre-saisine/envoi')->assertRedirect('/pre-saisine/confirmation');

        $this->assertSame(1, PresaisineSubmission::count());
    }

    public function test_a_student_must_give_a_card_number(): void
    {
        $this->throughOrientation()
            ->post('/pre-saisine/formulaire', $this->validForm(['is_uam_student' => '1']))
            ->assertSessionHasErrors('student_card_number');
    }

    public function test_the_honeypot_refuses_a_filled_in_field(): void
    {
        $this->throughOrientation()
            ->post('/pre-saisine/formulaire', $this->validForm(['website' => 'http://spam.test']))
            ->assertSessionHasErrors('website');
    }

    // --------------------------------------------------- attachments

    public function test_an_attachment_is_kept_on_the_private_disk(): void
    {
        $this->throughOrientation()
            ->post('/pre-saisine/formulaire', $this->validForm([
                'attachments' => [UploadedFile::fake()->create('contrat.pdf', 120, 'application/pdf')],
            ]))
            ->assertRedirect('/pre-saisine/verification');

        $this->post('/pre-saisine/envoi')->assertRedirect('/pre-saisine/confirmation');

        $attachment = PresaisineSubmission::first()->attachments->first();

        $this->assertSame('contrat.pdf', $attachment->original_name);
        // Moved out of the draft folder, beside its submission.
        $this->assertStringStartsWith('presaisine/'.now()->year.'/PS-', $attachment->path);
        Storage::disk('local')->assertExists($attachment->path);
    }

    public function test_more_than_five_attachments_are_refused(): void
    {
        $files = [];
        for ($i = 0; $i < 6; $i++) {
            $files[] = UploadedFile::fake()->create("piece-{$i}.pdf", 10, 'application/pdf');
        }

        $this->throughOrientation()
            ->post('/pre-saisine/formulaire', $this->validForm(['attachments' => $files]))
            ->assertSessionHasErrors('attachments');
    }

    public function test_an_attachment_of_the_wrong_type_is_refused(): void
    {
        $this->throughOrientation()
            ->post('/pre-saisine/formulaire', $this->validForm([
                'attachments' => [UploadedFile::fake()->create('script.exe', 10)],
            ]))
            ->assertSessionHasErrors('attachments.0');
    }

    public function test_an_attachment_over_five_megabytes_is_refused(): void
    {
        $this->throughOrientation()
            ->post('/pre-saisine/formulaire', $this->validForm([
                'attachments' => [UploadedFile::fake()->create('gros.pdf', 6000, 'application/pdf')],
            ]))
            ->assertSessionHasErrors('attachments.0');
    }

    // ------------------------------------------- sending and the rest

    public function test_a_sent_request_is_recorded_with_a_reference_and_a_consent(): void
    {
        $this->throughOrientation()
            ->post('/pre-saisine/formulaire', $this->validForm())
            ->assertRedirect('/pre-saisine/verification');

        $this->get('/pre-saisine/verification')
            ->assertOk()
            ->assertSee('Salaires non versés', escape: false);

        $this->post('/pre-saisine/envoi')->assertRedirect('/pre-saisine/confirmation');

        $submission = PresaisineSubmission::first();

        $this->assertMatchesRegularExpression('/^PS-\d{4}-\d{4}$/', $submission->reference);
        $this->assertNotNull($submission->consent_at);
        $this->assertSame(PresaisineSubmission::STATUS_PENDING, $submission->status);
        // The orientation that led here is kept with the request it produced.
        $this->assertSame('oui', $submission->orientation['domaine']);

        $this->get('/pre-saisine/confirmation')
            ->assertOk()
            ->assertSee($submission->reference, escape: false);
    }

    public function test_the_confirmation_is_not_reachable_on_its_own(): void
    {
        $this->get('/pre-saisine/confirmation')->assertRedirect('/pre-saisine');
    }

    /** Anti-spam, on top of the honeypot. */
    public function test_repeated_sendings_are_limited(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->throughOrientation()
                ->post('/pre-saisine/formulaire', $this->validForm(['subject' => "Demande {$i}"]));
            $this->post('/pre-saisine/envoi')->assertRedirect('/pre-saisine/confirmation');
        }

        $this->throughOrientation()->post('/pre-saisine/formulaire', $this->validForm());
        $this->post('/pre-saisine/envoi')->assertSessionHasErrors('envoi');

        $this->assertSame(3, PresaisineSubmission::count());
    }

    // ----------------------------------------------------- separation

    /** The one rule the whole design rests on. */
    public function test_a_request_is_never_written_to_the_public_database(): void
    {
        $this->throughOrientation()->post('/pre-saisine/formulaire', $this->validForm());
        $this->post('/pre-saisine/envoi');

        $this->assertSame(1, PresaisineSubmission::count());
        $this->assertSame('registre', (new PresaisineSubmission)->getConnectionName());
        $this->assertFalse(
            Schema::connection(config('database.default'))->hasTable('presaisine_submissions')
        );
    }
}
