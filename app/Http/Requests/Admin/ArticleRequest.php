<?php

namespace App\Http\Requests\Admin;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // The route already sits behind the auth middleware; content-editor
        // permissions arrive with the role matrix.
        return true;
    }

    public function rules(): array
    {
        $article = $this->route('article');

        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => [
                'nullable', 'string', 'max:200', 'alpha_dash',
                Rule::unique('articles', 'slug')->ignore($article?->id),
            ],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string', 'max:50000'],

            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where('type', 'article')],

            'status' => ['required', Rule::in([
                Article::STATUS_DRAFT,
                Article::STATUS_PUBLISHED,
                Article::STATUS_ARCHIVED,
            ])],

            'published_at' => ['nullable', 'date'],

            // 2 MB, matching the hint on the form. The type is re-checked
            // against the file's own bytes in MediaStorage.
            'image' => ['nullable', 'file', 'image', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'titre',
            'excerpt' => 'chapô',
            'body' => 'contenu',
            'category_id' => 'catégorie',
            'status' => 'statut',
            'published_at' => 'date de publication',
            'image' => 'image à la une',
        ];
    }

    public function messages(): array
    {
        return [
            'image.max' => "L'image ne doit pas dépasser 2 Mo.",
            'image.image' => "Le fichier doit être une image (JPG, PNG ou WebP).",
        ];
    }
}
