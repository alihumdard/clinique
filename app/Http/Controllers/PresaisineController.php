<?php

namespace App\Http\Controllers;

use App\Http\Requests\PresaisineRequest;
use App\Mail\PresaisineReceipt;
use App\Mail\PresaisineReceived;
use App\Models\LegalDomain;
use App\Models\Registre\PresaisineAttachment;
use App\Models\Registre\PresaisineSubmission;
use App\Support\Registre\PresaisineNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

/**
 * The public pré-saisine: orientation, form, review, confirmation.
 *
 * Two rules shape the whole journey.
 *
 * A visitor whose orientation answers stop the journey leaves no trace:
 * "ne pas conserver les réponses sous forme de dossier juridique si
 * l'utilisateur n'est pas dirigé vers le formulaire de dépôt". Their
 * answers live in the session and nowhere else.
 *
 * What is submitted is written to the Registre's database, never to the
 * public site's: a name, a telephone number and an account of someone's
 * problem are exactly what must stay out of it.
 */
class PresaisineController extends Controller
{
    /** Session keys for a journey in progress. */
    private const ORIENTATION = 'presaisine.orientation';
    private const DRAFT = 'presaisine.draft';
    private const DRAFT_FILES = 'presaisine.files';
    private const CONFIRMED = 'presaisine.reference';

    // 37. Introduction et orientation --------------------------------

    public function index(): View
    {
        return view('presaisine.orientation', [
            'questions' => config('presaisine.questions'),
            'provisional' => config('presaisine.provisional'),
        ]);
    }

    public function orient(Request $request): RedirectResponse|View
    {
        $questions = config('presaisine.questions');

        $answers = $request->validate(
            collect($questions)->mapWithKeys(
                fn (array $q) => ['reponses.'.$q['key'] => ['required', 'in:oui,non']]
            )->all(),
            [],
        )['reponses'];

        foreach ($questions as $question) {
            if (($answers[$question['key']] ?? null) !== $question['blocking']) {
                continue;
            }

            /*
             * Stopped here. The answers are not written anywhere: this
             * visitor is being told where else to go, and the plan is
             * explicit that their replies are not to be kept.
             */
            return view('presaisine.outcome', [
                'outcome' => config('presaisine.outcomes.'.$question['outcome']),
                'provisional' => config('presaisine.provisional'),
            ]);
        }

        $request->session()->put(self::ORIENTATION, $answers);

        return redirect()->route('presaisine.form');
    }

    // 38. Formulaire de demande --------------------------------------

    public function form(Request $request): RedirectResponse|View
    {
        if (! $request->session()->has(self::ORIENTATION)) {
            return redirect()->route('presaisine.index');
        }

        return view('presaisine.form', [
            'domains' => LegalDomain::active()->get(),
            'draft' => $request->session()->get(self::DRAFT, []),
            'files' => $request->session()->get(self::DRAFT_FILES, []),
            'attachments' => config('presaisine.attachments'),
        ]);
    }

    public function store(PresaisineRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['attachments', 'consent', 'website']);

        /*
         * Files cannot ride in the session, so they go to a draft folder
         * on the private disk now and are moved beside the submission
         * once it is sent. A draft folder left behind by someone who
         * never finished is swept by the module 4 retention job.
         */
        $stored = $request->session()->get(self::DRAFT_FILES, []);
        $token = $request->session()->get('presaisine.token') ?: Str::uuid()->toString();

        foreach ((array) $request->file('attachments', []) as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $stored[] = [
                'path' => $file->store("presaisine/brouillons/{$token}", 'local'),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ];
        }

        $stored = array_slice($stored, 0, config('presaisine.attachments.max_files'));

        $request->session()->put('presaisine.token', $token);
        $request->session()->put(self::DRAFT_FILES, $stored);
        $request->session()->put(self::DRAFT, $data);

        return redirect()->route('presaisine.review');
    }

    // 39. Vérification avant envoi -----------------------------------

    public function review(Request $request): RedirectResponse|View
    {
        if (! $request->session()->has(self::DRAFT)) {
            return redirect()->route('presaisine.form');
        }

        return view('presaisine.review', [
            'draft' => $request->session()->get(self::DRAFT),
            'files' => $request->session()->get(self::DRAFT_FILES, []),
            'orientation' => $request->session()->get(self::ORIENTATION, []),
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        if (! $request->session()->has(self::DRAFT)) {
            return redirect()->route('presaisine.form');
        }

        /*
         * Anti-spam, on top of the honeypot on the form: three sendings
         * from one address in ten minutes is far above what a person
         * needs and far below what a script would attempt.
         */
        $key = 'presaisine:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()->withErrors([
                'envoi' => "Trop de demandes envoyées depuis cet appareil. Merci de réessayer dans quelques minutes.",
            ]);
        }

        RateLimiter::hit($key, 600);

        $draft = $request->session()->get(self::DRAFT);
        $files = $request->session()->get(self::DRAFT_FILES, []);

        $submission = DB::connection('registre')->transaction(function () use ($request, $draft, $files) {
            $submission = PresaisineSubmission::create($draft + [
                'reference' => PresaisineNumber::next(),
                'orientation' => $request->session()->get(self::ORIENTATION, []),
                'status' => PresaisineSubmission::STATUS_PENDING,
                'consent_at' => now(),
                'ip_address' => $request->ip(),
            ]);

            foreach ($files as $file) {
                // Out of the draft folder and beside the submission, so a
                // swept draft folder can never take a sent file with it.
                $destination = 'presaisine/'.$submission->created_at->year.'/'.$submission->reference.'/'.basename($file['path']);
                Storage::disk('local')->move($file['path'], $destination);

                PresaisineAttachment::create([
                    'submission_id' => $submission->id,
                    'path' => $destination,
                    'original_name' => $file['original_name'],
                    'mime_type' => $file['mime_type'],
                    'size' => $file['size'],
                ]);
            }

            return $submission;
        });

        $this->notify($submission);

        $request->session()->forget([self::ORIENTATION, self::DRAFT, self::DRAFT_FILES, 'presaisine.token']);
        $request->session()->put(self::CONFIRMED, $submission->reference);

        return redirect()->route('presaisine.confirmation');
    }

    /**
     * The two messages the plan asks for: "envoyer un accusé de réception
     * par e-mail si une adresse est fournie et notifier la Clinique pour
     * chaque demande valide".
     *
     * Sent after the request is safely stored, and never allowed to undo
     * it. A mail server that is down, misconfigured or simply not set up
     * yet must not cost someone the request they just spent ten minutes
     * writing: the failure is logged and the visitor still gets their
     * reference.
     */
    private function notify(PresaisineSubmission $submission): void
    {
        try {
            if (filled($submission->email)) {
                Mail::to($submission->email)->send(new PresaisineReceipt($submission));
            }

            Mail::to(config('clinique.contact.email'))->send(new PresaisineReceived($submission));
        } catch (Throwable $e) {
            Log::error('Pré-saisine : notification non envoyée.', [
                'reference' => $submission->reference,
                'exception' => $e->getMessage(),
            ]);
        }
    }

    // 40. Confirmation / numéro de référence -------------------------

    public function confirmation(Request $request): RedirectResponse|View
    {
        $reference = $request->session()->get(self::CONFIRMED);

        if (! $reference) {
            return redirect()->route('presaisine.index');
        }

        return view('presaisine.confirmation', ['reference' => $reference]);
    }
}
