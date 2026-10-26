<?php

namespace App\Http\Requests\Admin;

use App\Models\Report;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        // The route already sits behind the auth middleware; content-editor
        // permissions arrive with the role matrix.
        return true;
    }

    public function rules(): array
    {
        $report = $this->route('report');

        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => [
                'nullable', 'string', 'max:200', 'alpha_dash',
                Rule::unique('reports', 'slug')->ignore($report?->id),
            ],

            // The Clinic opened in 2019; a report dated before that, or more
            // than a year ahead, is a typo rather than a record.
            'year' => ['required', 'integer', 'min:2019', 'max:'.(now()->year + 1)],

            'description' => ['nullable', 'string', 'max:2000'],

            'status' => ['required', Rule::in([
                Report::STATUS_DRAFT,
                Report::STATUS_PUBLISHED,
                Report::STATUS_ARCHIVED,
            ])],

            'published_at' => ['nullable', 'date'],

            'cover' => ['nullable', 'file', 'image', 'max:2048'],

            // A new report needs its PDF; an edit may keep the one it has.
            'document' => [
                $report ? 'nullable' : 'required',
                'file', 'mimes:pdf', 'max:20480',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'titre',
            'year' => 'année',
            'description' => 'description',
            'status' => 'statut',
            'published_at' => 'date de publication',
            'cover' => 'image de couverture',
            'document' => 'fichier PDF',
        ];
    }

    public function messages(): array
    {
        return [
            'document.required' => 'Un rapport doit être accompagné de son fichier PDF.',
            'document.mimes' => 'Le fichier doit être un PDF.',
            'document.max' => 'Le PDF ne doit pas dépasser 20 Mo.',
            'year.min' => "L'année doit être 2019 ou postérieure.",
            'cover.max' => "L'image ne doit pas dépasser 2 Mo.",
        ];
    }
}
