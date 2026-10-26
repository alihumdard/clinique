<?php

namespace App\Http\Requests\Admin;

use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
{
    /**
     * Paths the site's own routes already own.
     *
     * A CMS page is served from the catch-all at the end of the route
     * table, so one of these slugs would never be reached — the fixed
     * route matches first. Refusing it here is clearer than letting the
     * Clinic save a page that silently never appears.
     *
     * @var array<int, string>
     */
    public const RESERVED = [
        'a-propos', 'partenaires', 'services', 'actualites', 'activites',
        'galerie', 'ressources', 'web-tv', 'rejoindre', 'contact',
        'recherche', 'pre-saisine', 'mentions-legales',
        'conditions-utilisation', 'politique-confidentialite',
        'admin', 'newsletter', 'storage', 'build', 'images', '404',
    ];

    public function authorize(): bool
    {
        // The route already sits behind the auth middleware; content-editor
        // permissions arrive with the role matrix.
        return true;
    }

    public function rules(): array
    {
        $page = $this->route('page');

        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => [
                'required', 'string', 'max:200', 'alpha_dash',
                Rule::notIn(self::RESERVED),
                Rule::unique('pages', 'slug')->ignore($page?->id),
            ],
            'body' => ['nullable', 'string', 'max:100000'],

            'status' => ['required', Rule::in([
                Page::STATUS_DRAFT,
                Page::STATUS_PUBLISHED,
                Page::STATUS_ARCHIVED,
            ])],

            'published_at' => ['nullable', 'date'],

            'seo_title' => ['nullable', 'string', 'max:70'],
            'seo_description' => ['nullable', 'string', 'max:300'],

            'image' => ['nullable', 'file', 'image', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'titre',
            'slug' => 'adresse de la page',
            'body' => 'contenu',
            'status' => 'statut',
            'seo_title' => 'titre SEO',
            'seo_description' => 'description SEO',
            'image' => 'image',
        ];
    }

    public function messages(): array
    {
        return [
            'slug.required' => "L'adresse de la page est nécessaire : c'est son URL.",
            'slug.alpha_dash' => "L'adresse ne peut contenir que des lettres, des chiffres et des tirets.",
            'slug.not_in' => 'Cette adresse est déjà utilisée par une page du site.',
            'slug.unique' => 'Une autre page utilise déjà cette adresse.',
        ];
    }
}
