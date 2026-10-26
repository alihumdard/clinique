<?php

namespace App\Http\Requests\Admin;

use App\Models\Video;
use App\Support\VideoLink;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class VideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // The route already sits behind the auth middleware; content-editor
        // permissions arrive with the role matrix.
        return true;
    }

    public function rules(): array
    {
        $video = $this->route('video');

        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => [
                'nullable', 'string', 'max:200', 'alpha_dash',
                Rule::unique('videos', 'slug')->ignore($video?->id),
            ],
            'summary' => ['nullable', 'string', 'max:1000'],

            'video_url' => ['required', 'url', 'max:500'],

            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where('type', 'video')],

            // mm:ss or h:mm:ss, matching the hint on the form.
            'duration' => ['nullable', 'string', 'regex:/^\d{1,3}:[0-5]\d$/'],

            'transcript' => ['nullable', 'string', 'max:100000'],

            'status' => ['required', Rule::in([
                Video::STATUS_DRAFT,
                Video::STATUS_PUBLISHED,
                Video::STATUS_ARCHIVED,
            ])],

            'published_at' => ['nullable', 'date'],
            'allow_comments' => ['nullable', 'boolean'],

            // 2 MB, matching the hint. The type is re-checked against the
            // file's own bytes in MediaStorage.
            'cover' => ['nullable', 'file', 'image', 'max:2048'],
        ];
    }

    /**
     * A well-formed URL is not enough: it has to be an address we can
     * actually embed, or the capsule would publish with a dead player.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->filled('video_url') && ! VideoLink::parse($this->input('video_url'))) {
                    $validator->errors()->add(
                        'video_url',
                        'Le lien doit être une adresse YouTube ou Vimeo (par exemple https://youtu.be/xxxxxxxxxxx).'
                    );
                }
            },
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'titre',
            'summary' => 'description',
            'video_url' => 'lien vidéo',
            'category_id' => 'catégorie',
            'duration' => 'durée',
            'status' => 'statut',
            'published_at' => 'date de publication',
            'cover' => 'image de couverture',
        ];
    }

    public function messages(): array
    {
        return [
            'duration.regex' => 'La durée doit être au format mm:ss (par exemple 12:35).',
            'cover.max' => "L'image ne doit pas dépasser 2 Mo.",
            'cover.image' => 'Le fichier doit être une image (JPG, PNG ou WebP).',
        ];
    }
}
