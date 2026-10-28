<?php

namespace App\Mail;

use App\Models\Registre\PresaisineSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Tells the Clinic that a request is waiting: "notifier la Clinique pour
 * chaque demande valide".
 *
 * It says that one arrived, what domain it concerns and how urgent the
 * orientation suggested it is — enough to decide whether to open the
 * registre now or at the next permanence. The request itself stays in
 * the registre, behind a sign-in, and is not copied into an e-mail.
 */
class PresaisineReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PresaisineSubmission $submission) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle pré-saisine '.$this->submission->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.presaisine.received',
            with: [
                'submission' => $this->submission,
                'url' => route('registre.submissions.show', $this->submission),
            ],
        );
    }
}
