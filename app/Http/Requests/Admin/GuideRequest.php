<?php

namespace App\Http\Requests\Admin;

use App\Models\Guide;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuideRequest extends FormRequest
{
    public function authorize(): bool
    {
        // The route already sits behind the auth middleware; content-editor
        // permissions arrive with the role matrix.
        return true;
    }

    public function rules(): array
    {
        $guide = $this->route('guide');

        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => [
                'nullable', 'string', 'max:200', 'alpha_dash',
                Rule::unique('guides', 'slug')->ignore($guide?->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'body' => ['nullable', 'string', 'max:50000'],

            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where('type', 'guide')],

            'status' => ['required', Rule::in([
                Guide::STATUS_DRAFT,
                Guide::STATUS_PUBLISHED,
                Guide::STATUS_ARCHIVED,
            ])],

            'published_at' => ['nullable', 'date'],

            'cover' => ['nullable', 'file', 'image', 'max:2048'],

            // A new fiche needs its PDF; an edit may keep the one it has.
            // 10 MB, matching the hint on the form. The type is re-checked
            // against the file's own bytes in MediaStorage.
            'document' => [
                $guide ? 'nullable' : 'required',
                'file', 'mimes:pdf', 'max:10240',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'titre',
            'description' => 'description',
            'body' => 'contenu',
            'category_id' => 'domaine',
            'status' => 'statut',
            'published_at' => 'date de publication',
            'cover' => 'image de couverture',
            'document' => 'fichier PDF',
        ];
    }

    public function messages(): array
    {
        return [
            'document.required' => 'Une fiche pratique doit être accompagnée de son fichier PDF.',
            'document.mimes' => 'Le fichier doit être un PDF.',
            'document.max' => 'Le PDF ne doit pas dépasser 10 Mo.',
            'cover.max' => "L'image ne doit pas dépasser 2 Mo.",
            'cover.image' => 'Le fichier doit être une image (JPG, PNG ou WebP).',
        ];
    }
}
