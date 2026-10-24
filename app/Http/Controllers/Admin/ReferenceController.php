<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\LegalDomain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * The two reference lists: the fee grid and the legal domains.
 *
 * Neither has a screen in the design, and neither warrants one: each is a
 * flat ordered list of a dozen short rows, edited in place. They share a
 * screen rather than taking two, and the sidebar calls it Référentiels.
 *
 * The domains matter beyond the public pages — the plan has them feeding
 * the pré-saisine form and the register in Module 3, which is why it asks
 * the Clinic to keep the list in one place.
 */
class ReferenceController extends Controller
{
    public function index(): View
    {
        return view('admin.references', [
            'fees' => Fee::orderBy('position')->orderBy('label')->get(),
            'domains' => LegalDomain::orderBy('position')->orderBy('name')->get(),
            // The icons a domain may carry, so the Clinic picks from what
            // the icon set actually holds rather than typing a name.
            'icons' => self::ICONS,
            'tones' => self::TONES,
        ]);
    }

    /** @var array<string, string> */
    public const ICONS = [
        'users' => 'Personnes',
        'scale' => 'Balance',
        'gavel' => 'Marteau',
        'heart' => 'Cœur',
        'building' => 'Bâtiment',
        'handshake' => 'Poignée de main',
        'map-pin' => 'Lieu',
        'document' => 'Document',
        'book' => 'Livre',
        'info' => 'Information',
        'graduation-cap' => 'Formation',
        'folder' => 'Dossier',
    ];

    /** @var array<string, string> */
    public const TONES = [
        'blue' => 'Bleu',
        'green' => 'Vert',
        'amber' => 'Ambre',
        'red' => 'Rouge',
        'purple' => 'Violet',
        'pink' => 'Rose',
        'slate' => 'Gris',
    ];

    /*
    |--------------------------------------------------------------------------
    | Grille tarifaire
    |--------------------------------------------------------------------------
    */

    public function storeFee(Request $request): RedirectResponse
    {
        Fee::create($this->feeData($request));

        return back()->with('status', 'Le tarif a été ajouté.');
    }

    public function updateFee(Request $request, Fee $fee): RedirectResponse
    {
        $fee->update($this->feeData($request, $fee));

        return back()->with('status', "Le tarif « {$fee->label} » a été mis à jour.");
    }

    public function destroyFee(Fee $fee): RedirectResponse
    {
        $label = $fee->label;
        $fee->delete();

        return back()->with('status', "Le tarif « {$label} » a été supprimé.");
    }

    /** @return array<string, mixed> */
    private function feeData(Request $request, ?Fee $existing = null): array
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:160'],
            'note' => ['nullable', 'string', 'max:500'],
            'amount' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            // Used where a fee is not a single figure: a range, or
            // "Selon tarif en vigueur".
            'amount_label' => ['nullable', 'string', 'max:80'],
            'is_free' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ], [], [
            'label' => 'nature de la prestation',
            'note' => 'description',
            'amount' => 'montant',
            'amount_label' => 'montant (texte)',
        ]);

        $data['is_free'] = $request->boolean('is_free');
        $data['is_visible'] = $request->boolean('is_visible', true);
        $data['position'] = $data['position']
            ?? $existing?->position
            ?? ((int) Fee::max('position') + 1);

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Domaines du droit
    |--------------------------------------------------------------------------
    */

    public function storeDomain(Request $request): RedirectResponse
    {
        LegalDomain::create($this->domainData($request));

        return back()->with('status', 'Le domaine a été ajouté.');
    }

    public function updateDomain(Request $request, LegalDomain $domain): RedirectResponse
    {
        $domain->update($this->domainData($request, $domain));

        return back()->with('status', "Le domaine « {$domain->name} » a été mis à jour.");
    }

    public function destroyDomain(LegalDomain $domain): RedirectResponse
    {
        $name = $domain->name;
        $domain->delete();

        return back()->with('status', "Le domaine « {$name} » a été supprimé.");
    }

    /** @return array<string, mixed> */
    private function domainData(Request $request, ?LegalDomain $existing = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            /*
             * The plain-language wording the plan asks for: "Droit de la
             * famille" is the legal name, "Mariage, divorce, enfants" is
             * what a visitor recognises.
             */
            'plain_language' => ['nullable', 'string', 'max:160'],
            'icon' => ['nullable', Rule::in(array_keys(self::ICONS))],
            'tone' => ['nullable', Rule::in(array_keys(self::TONES))],
            'is_active' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ], [], [
            'name' => 'nom du domaine',
            'plain_language' => 'formulation simple',
        ]);

        $data['slug'] = $existing?->slug ?? $this->uniqueDomainSlug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['icon'] = $data['icon'] ?? 'scale';
        $data['tone'] = $data['tone'] ?? 'blue';
        $data['position'] = $data['position']
            ?? $existing?->position
            ?? ((int) LegalDomain::max('position') + 1);

        return $data;
    }

    /**
     * The slug is fixed at creation and never follows a rename: Module 3
     * stores it on a dossier, so changing it would orphan those records.
     */
    private function uniqueDomainSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'domaine';
        $candidate = $base;
        $suffix = 2;

        while (LegalDomain::where('slug', $candidate)->exists()) {
            $candidate = "{$base}-{$suffix}";
            $suffix++;
        }

        return $candidate;
    }
}
