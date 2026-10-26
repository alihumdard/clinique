<?php

namespace App\Http\Requests\Admin;

use App\Models\Partner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        // The route already sits behind the auth middleware; content-editor
        // permissions arrive with the role matrix.
        return true;
    }

    public function rules(): array
    {
        $partner = $this->route('partner');

        return [
            'name' => ['required', 'string', 'max:160'],
            'slug' => [
                'nullable', 'string', 'max:160', 'alpha_dash',
                Rule::unique('partners', 'slug')->ignore($partner?->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],

            // A partner's own site: http or https only, so a javascript:
            // or data: address can never end up in an href.
            'website' => ['nullable', 'url:http,https', 'max:255'],

            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where('type', 'partner')],

            'status' => ['required', Rule::in([
                Partner::STATUS_DRAFT,
                Partner::STATUS_PUBLISHED,
                Partner::STATUS_ARCHIVED,
            ])],

            'is_visible' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer', 'min:0', 'max:9999'],

            /*
             * SVG is deliberately absent although the mockup lists it for
             * partner logos: an SVG is a document that can carry script,
             * and it would be served from our own origin.
             */
            'logo' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nom',
            'description' => 'description',
            'website' => 'site web',
            'category_id' => 'catégorie',
            'status' => 'statut',
            'position' => 'ordre',
            'logo' => 'logo',
        ];
    }

    public function messages(): array
    {
        return [
            'logo.max' => 'Le logo ne doit pas dépasser 1 Mo.',
            'logo.mimes' => 'Le logo doit être au format JPG, PNG ou WebP (le SVG n\'est pas accepté).',
            'website.url' => "L'adresse du site doit commencer par http:// ou https://.",
        ];
    }
}
