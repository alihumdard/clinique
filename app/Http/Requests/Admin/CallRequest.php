<?php

namespace App\Http\Requests\Admin;

use App\Models\Call;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CallRequest extends FormRequest
{
    public function authorize(): bool
    {
        // The route already sits behind the auth middleware; content-editor
        // permissions arrive with the role matrix.
        return true;
    }

    public function rules(): array
    {
        $call = $this->route('call');

        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => [
                'nullable', 'string', 'max:200', 'alpha_dash',
                Rule::unique('calls', 'slug')->ignore($call?->id),
            ],
            'excerpt' => ['nullable', 'string', 'max:800'],
            'body' => ['nullable', 'string', 'max:50000'],

            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where('type', 'call')],

            // The closing date is what drops a call off the public list, so
            // a date in the past is accepted: it is how a call is closed.
            'closes_on' => ['nullable', 'date'],

            'status' => ['required', Rule::in([
                Call::STATUS_DRAFT,
                Call::STATUS_PUBLISHED,
                Call::STATUS_ARCHIVED,
            ])],

            'published_at' => ['nullable', 'date'],

            'image' => ['nullable', 'file', 'image', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'titre',
            'excerpt' => 'résumé',
            'body' => 'contenu',
            'category_id' => "type d'appel",
            'closes_on' => 'date de clôture',
            'status' => 'statut',
            'image' => 'image',
        ];
    }
}
