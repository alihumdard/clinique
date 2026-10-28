<?php

namespace App\Mail;

use App\Models\Registre\PresaisineSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The acknowledgement sent to someone who left a pré-saisine, when they
 * gave an address: "envoyer un accusé de réception par e-mail si une
 * adresse est fournie".
 *
 * It carries the reference and nothing else of the request. An e-mail
 * travels unencrypted and lands in an inbox the Clinic does not control,
 * so repeating the account of someone's situation back to them there
 * would put it somewhere it does not need to be.
 */
class PresaisineReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PresaisineSubmission $submission) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre pré-saisine '.$this->submission->reference.' — Clinique Juridique FSJP-UAM',
            replyTo: [config('clinique.contact.email')],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.presaisine.receipt',
            with: [
                'reference' => $this->submission->reference,
                'name' => $this->submission->first_name ?: $this->submission->last_name,
            ],
        );
    }
}
