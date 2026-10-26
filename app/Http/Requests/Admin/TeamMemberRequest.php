<?php

namespace App\Http\Requests\Admin;

use App\Models\TeamMember;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeamMemberRequest extends FormRequest
{
    /** The groups the public team page is divided into. */
    public const DEPARTMENTS = [
        'direction' => 'Direction',
        'enseignants' => 'Enseignants encadrants',
        'etudiants' => 'Étudiants membres',
        'administratif' => 'Personnel administratif et technique',
    ];

    public function authorize(): bool
    {
        // The route already sits behind the auth middleware; content-editor
        // permissions arrive with the role matrix.
        return true;
    }

    public function rules(): array
    {
        $member = $this->route('member');

        return [
            'name' => ['required', 'string', 'max:160'],
            'slug' => [
                'nullable', 'string', 'max:160', 'alpha_dash',
                Rule::unique('team_members', 'slug')->ignore($member?->id),
            ],
            'role' => ['required', 'string', 'max:160'],
            'bio' => ['nullable', 'string', 'max:1000'],

            'department' => ['required', Rule::in(array_keys(self::DEPARTMENTS))],
            'cohort' => ['nullable', 'string', 'max:32'],

            /*
             * These rows describe identifiable people. The address is only
             * published where the member's own entry carries one, and it is
             * validated so a malformed value never reaches a mailto:.
             */
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'linkedin' => ['nullable', 'url:https', 'max:255'],

            'status' => ['required', Rule::in([
                TeamMember::STATUS_DRAFT,
                TeamMember::STATUS_PUBLISHED,
                TeamMember::STATUS_ARCHIVED,
            ])],

            'is_visible' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer', 'min:0', 'max:9999'],

            'photo' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nom',
            'role' => 'fonction',
            'bio' => 'présentation',
            'department' => 'groupe',
            'cohort' => 'promotion',
            'email' => 'adresse e-mail',
            'linkedin' => 'profil LinkedIn',
            'status' => 'statut',
            'position' => 'ordre',
            'photo' => 'photo',
        ];
    }

    public function messages(): array
    {
        return [
            'photo.max' => 'La photo ne doit pas dépasser 2 Mo.',
            'linkedin.url' => "L'adresse LinkedIn doit commencer par https://.",
        ];
    }
}
