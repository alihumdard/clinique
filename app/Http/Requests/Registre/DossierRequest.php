<?php

namespace App\Http\Requests\Registre;

use App\Models\Registre\Dossier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A dossier opened by hand, for someone received at the permanence:
 * "permettre au personnel autorisé de créer manuellement des dossiers
 * pour les personnes reçues sur place".
 *
 * The required fields are the ones the online pré-saisine also insists
 * on — name, telephone, domain, subject — so a walk-in dossier is never
 * thinner than one that arrived online. The complete field list is among
 * the "champs définitifs du dossier" still to be confirmed.
 */
class DossierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'last_name' => ['required', 'string', 'max:120'],
            'first_name' => ['nullable', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:255'],
            'commune' => ['nullable', 'string', 'max:120'],
            'is_uam_student' => ['nullable', 'boolean'],
            'student_card_number' => ['nullable', 'required_if:is_uam_student,1', 'string', 'max:40'],

            'legal_domain' => ['required', 'string', 'max:120'],
            'subject' => ['required', 'string', 'max:200'],
            'narrative' => ['nullable', 'string', 'max:5000'],
            'question_1' => ['nullable', 'string', 'max:500'],
            'question_2' => ['nullable', 'string', 'max:500'],
            'question_3' => ['nullable', 'string', 'max:500'],

            'urgency' => ['required', Rule::in([
                Dossier::URGENCY_NORMAL,
                Dossier::URGENCY_HIGH,
                Dossier::URGENCY_CRITICAL,
            ])],

            'opened_at' => ['nullable', 'date', 'before_or_equal:today'],
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
            'student_card_number' => "numéro de carte d'étudiant",
            'legal_domain' => 'domaine juridique',
            'subject' => 'objet',
            'narrative' => 'exposé',
            'urgency' => 'urgence',
            'opened_at' => "date d'ouverture",
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'opened_at.before_or_equal' => "Un dossier ne peut pas être ouvert à une date future.",
            'student_card_number.required_if' => "Indiquez le numéro de carte d'étudiant.",
        ];
    }
}
