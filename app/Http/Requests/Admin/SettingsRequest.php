<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        // The route already sits behind the auth middleware; the
        // administrator-only restriction arrives with the role matrix.
        return true;
    }

    public function rules(): array
    {
        return [
            'site_name' => ['required', 'string', 'max:160'],
            'site_faculty' => ['nullable', 'string', 'max:160'],
            'site_university' => ['nullable', 'string', 'max:160'],
            'site_description' => ['required', 'string', 'max:600'],

            'contact_phone' => ['nullable', 'string', 'max:40'],
            // The href form: digits and a leading + only, since it goes
            // straight into a tel: link.
            'contact_phone_href' => ['nullable', 'string', 'max:24', 'regex:/^\+?[0-9]+$/'],
            'contact_email' => ['required', 'email:rfc', 'max:255'],

            'contact_address_line1' => ['nullable', 'string', 'max:200'],
            'contact_address_line2' => ['nullable', 'string', 'max:200'],
            'contact_address_city' => ['nullable', 'string', 'max:120'],
            'contact_address_country' => ['nullable', 'string', 'max:120'],

            'hours_days' => ['nullable', 'string', 'max:120'],
            'hours_time' => ['nullable', 'string', 'max:120'],
            'hours_note' => ['nullable', 'string', 'max:200'],

            // Social addresses end up in href attributes, so https only.
            'social_facebook' => ['nullable', 'url:https', 'max:255'],
            'social_x' => ['nullable', 'url:https', 'max:255'],
            'social_linkedin' => ['nullable', 'url:https', 'max:255'],
            'social_youtube' => ['nullable', 'url:https', 'max:255'],
            'social_instagram' => ['nullable', 'url:https', 'max:255'],

            'seo_title' => ['nullable', 'string', 'max:70'],
            'seo_description' => ['nullable', 'string', 'max:300'],

            // The bandeau d'état: a notice shown on every page.
            'banner_enabled' => ['nullable', 'boolean'],
            'banner_message' => ['nullable', 'string', 'max:300', 'required_with:banner_enabled'],
            'banner_tone' => ['nullable', Rule::in(['info', 'warning', 'closure'])],

            // Whether the permanence is receiving visitors today. Three
            // states, the ones the strip already draws.
            'service_state' => ['nullable', Rule::in(['open', 'closed', 'notice'])],

            /*
             * Logo, favicon and the default sharing image. SVG is refused
             * here as everywhere: it is a document that can carry script,
             * and the logo in particular is served on every page.
             */
            'logo' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'favicon' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'seo_image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],

            // Which of the three to put back to the bundled default.
            'remove' => ['nullable', 'array'],
            'remove.*' => [Rule::in(['logo', 'favicon', 'seo_image'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'site_name' => 'nom du site',
            'site_description' => 'description courte',
            'contact_phone' => 'téléphone',
            'contact_phone_href' => 'téléphone (format international)',
            'contact_email' => 'adresse e-mail',
            'seo_title' => 'titre SEO',
            'seo_description' => 'description SEO',
            'banner_message' => 'message du bandeau',
            'banner_tone' => 'type de bandeau',
        ];
    }

    public function messages(): array
    {
        return [
            'contact_phone_href.regex' => 'Le téléphone au format international ne doit contenir que des chiffres, éventuellement précédés de +.',
            'banner_message.required_with' => "Un bandeau activé a besoin d'un message.",
            'social_facebook.url' => 'Le lien Facebook doit commencer par https://.',
            'social_x.url' => 'Le lien X doit commencer par https://.',
            'social_linkedin.url' => 'Le lien LinkedIn doit commencer par https://.',
            'social_youtube.url' => 'Le lien YouTube doit commencer par https://.',
            'social_instagram.url' => 'Le lien Instagram doit commencer par https://.',
            'seo_title.max' => 'Le titre SEO ne doit pas dépasser 70 caractères (limite affichée par les moteurs de recherche).',
        ];
    }
}
