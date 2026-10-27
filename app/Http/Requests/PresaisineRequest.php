<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * The pré-saisine request form.
 *
 * The plan's minimum is "le nom, le téléphone, le domaine et l'objet de
 * la demande", and adds that "tout autre champ obligatoire" must be
 * confirmed before implementation — so nothing else is required here,
 * however useful it would be to have.
 */
class PresaisineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $attachments = config('presaisine.attachments');

        return [
            'last_name' => ['required', 'string', 'max:120'],
            'first_name' => ['nullable', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:255'],
            'commune' => ['nullable', 'string', 'max:120'],

            'is_uam_student' => ['nullable', 'boolean'],
            // Only asked of a student, and only kept if they said so.
            'student_card_number' => ['nullable', 'required_if:is_uam_student,1', 'string', 'max:40'],

            'legal_domain' => ['required', 'string', 'max:120'],
            'subject' => ['required', 'string', 'max:200'],
            'narrative' => ['nullable', 'string', 'max:5000'],

            'question_1' => ['nullable', 'string', 'max:500'],
            'question_2' => ['nullable', 'string', 'max:500'],
            'question_3' => ['nullable', 'string', 'max:500'],

            // "jusqu'à cinq pièces jointes PDF/JPEG/PNG, de 5 Mo maximum"
            'attachments' => ['nullable', 'array', 'max:'.$attachments['max_files']],
            'attachments.*' => [
                'file',
                'mimes:'.implode(',', $attachments['mimes']),
                'max:'.$attachments['max_size_kb'],
            ],

            // "le consentement explicite au traitement des données"
            'consent' => ['accepted'],

            // A field nobody sees and a script usually fills in.
            'website' => ['prohibited'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'last_name' => 'nom',
            'first_name' => 'prénom',
            'phone' => 'téléphone',
            'email' => 'adresse e-mail',
            'commune' => 'commune',
            'student_card_number' => 'numéro de carte d\'étudiant',
            'legal_domain' => 'domaine juridique',
            'subject' => 'objet de la demande',
            'narrative' => 'exposé de la situation',
            'attachments' => 'pièces jointes',
            'consent' => 'consentement',
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'consent.accepted' => 'Votre demande ne peut être enregistrée sans votre accord sur le traitement des données.',
            'attachments.max' => 'Vous pouvez joindre au maximum :max fichiers.',
            'attachments.*.mimes' => 'Les pièces jointes doivent être au format PDF, JPG ou PNG.',
            'attachments.*.max' => 'Chaque pièce jointe doit peser moins de 5 Mo.',
            'student_card_number.required_if' => "Indiquez votre numéro de carte d'étudiant.",
        ];
    }
}
