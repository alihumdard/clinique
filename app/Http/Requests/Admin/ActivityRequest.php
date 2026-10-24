<?php

namespace App\Http\Requests\Admin;

use App\Models\Activity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        // The route already sits behind the auth middleware; content-editor
        // permissions arrive with the role matrix.
        return true;
    }

    public function rules(): array
    {
        $activity = $this->route('activity');

        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => [
                'nullable', 'string', 'max:200', 'alpha_dash',
                Rule::unique('activities', 'slug')->ignore($activity?->id),
            ],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string', 'max:50000'],

            'location' => ['nullable', 'string', 'max:160'],
            'held_on' => ['nullable', 'date'],

            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where('type', 'activity')],

            'status' => ['required', Rule::in([
                Activity::STATUS_DRAFT,
                Activity::STATUS_PUBLISHED,
                Activity::STATUS_ARCHIVED,
            ])],

            'published_at' => ['nullable', 'date'],

            'image' => ['nullable', 'file', 'image', 'max:2048'],

            // The gallery: several files at once, each checked on its own.
            // 20 per submission keeps one request within PHP's own
            // max_file_uploads, which silently drops the rest beyond it.
            'photos' => ['nullable', 'array', 'max:20'],
            'photos.*' => ['file', 'image', 'max:5120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'titre',
            'excerpt' => 'description',
            'body' => 'contenu',
            'location' => 'lieu',
            'held_on' => "date de l'activité",
            'category_id' => "type d'activité",
            'status' => 'statut',
            'published_at' => 'date de publication',
            'image' => 'image principale',
            'photos' => 'galerie photos',
        ];
    }

    public function messages(): array
    {
        return [
            'photos.max' => 'Vous pouvez ajouter 20 photos au maximum à la fois.',
            'photos.*.image' => 'Chaque fichier de la galerie doit être une image (JPG, PNG ou WebP).',
            'photos.*.max' => 'Chaque photo ne doit pas dépasser 5 Mo.',
            'image.max' => "L'image principale ne doit pas dépasser 2 Mo.",
        ];
    }
}
