<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactRequest extends FormRequest
{
    /** The subjects the form offers; anything else was not chosen from it. */
    public const SUBJECTS = [
        'information' => 'Informations générales',
        'assistance' => "Demande d'assistance",
        'partenariat' => 'Partenariats',
        'recrutement' => 'Recrutement',
        'autre' => 'Autre',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:160'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['required', Rule::in(array_keys(self::SUBJECTS))],
            'message' => ['required', 'string', 'min:10', 'max:5000'],

            /*
             * The plan asks for an explicit agreement, separate from any
             * acceptance of the terms, and records when it was given. So
             * the box is required rather than merely noted.
             */
            'consent' => ['accepted'],

            // A field nobody sees and a script usually fills in.
            'website' => ['prohibited'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nom complet',
            'email' => 'adresse e-mail',
            'phone' => 'téléphone',
            'subject' => 'sujet',
            'message' => 'message',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Merci d\'indiquer votre nom.',
            'email.required' => 'Merci d\'indiquer votre adresse e-mail.',
            'email.email' => "Cette adresse e-mail n'est pas valide.",
            'subject.required' => 'Merci de choisir un sujet.',
            'subject.in' => 'Merci de choisir un sujet dans la liste.',
            'message.required' => 'Merci de décrire votre demande.',
            'message.min' => 'Merci de décrire votre demande en quelques mots de plus.',
            'consent.accepted' => 'Votre accord est nécessaire pour que nous puissions traiter votre demande.',
        ];
    }
}
