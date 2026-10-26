<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ManagesContent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PartnerRequest;
use App\Models\Category;
use App\Models\Partner;
use App\Services\MediaStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PartnerController extends Controller
{
    use ManagesContent;

    public function __construct(private readonly MediaStorage $media)
    {
    }

    public function index(Request $request): View
    {
        $partners = $this->applyFilters(
            Partner::query()->with('category'),
            $request,
            ['name', 'description']
        )
            // The Clinic's own order first, then alphabetical within it.
            ->orderBy('position')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.partners', [
            'partners' => $partners,
            'categories' => Category::ofType('partner')->withCount('partners')->get(),
            'counts' => $this->listCounts(Partner::class),
            'filters' => $request->only(['statut', 'categorie', 'q']),
        ]);
    }

    public function store(PartnerRequest $request): RedirectResponse
    {
        $data = $this->prepare($request);

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->media->storeImage($request->file('logo'), 'partenaires');
        }

        $partner = Partner::create($data);

        return redirect()
            ->route('admin.partners')
            ->with('status', "Le partenaire « {$partner->name} » a été enregistré.");
    }

    public function update(PartnerRequest $request, Partner $partner): RedirectResponse
    {
        $data = $this->prepare($request, $partner);

        if ($request->hasFile('logo')) {
            // Replace, then drop the old file — not the other way round, so
            // a failed upload cannot leave the row pointing at nothing.
            $previous = $partner->logo;
            $data['logo'] = $this->media->storeImage($request->file('logo'), 'partenaires');
            $this->media->deleteImage($previous);
        }

        $partner->update($data);

        return redirect()
            ->route('admin.partners')
            ->with('status', "Le partenaire « {$partner->name} » a été mis à jour.");
    }

    public function destroy(Partner $partner): RedirectResponse
    {
        $partner->delete();

        return redirect()
            ->route('admin.partners')
            ->with('status', "Le partenaire « {$partner->name} » a été mis à la corbeille.");
    }

    public function restore(int $id): RedirectResponse
    {
        $partner = Partner::onlyTrashed()->findOrFail($id);
        $partner->restore();

        return redirect()
            ->route('admin.partners')
            ->with('status', "Le partenaire « {$partner->name} » a été restauré.");
    }

    /** Permanent: the row and its logo go for good. */
    public function forceDestroy(int $id): RedirectResponse
    {
        $partner = Partner::onlyTrashed()->findOrFail($id);
        $name = $partner->name;

        $this->media->deleteImage($partner->logo);
        $partner->forceDelete();

        return redirect()
            ->route('admin.partners')
            ->with('status', "Le partenaire « {$name} » a été supprimé définitivement.");
    }

    /**
     * Shows or hides one partner without touching anything else about it.
     * A hidden partner is still a correct record, simply not on the site.
     */
    public function toggle(Partner $partner): RedirectResponse
    {
        $partner->update(['is_visible' => ! $partner->is_visible]);

        return back()->with('status', $partner->is_visible
            ? "Le partenaire « {$partner->name} » est désormais visible."
            : "Le partenaire « {$partner->name} » est désormais masqué.");
    }

    /** @return array<string, mixed> */
    private function prepare(PartnerRequest $request, ?Partner $existing = null): array
    {
        $data = $request->validated();

        $data['slug'] = $this->uniqueSlug(Partner::class, $data['slug'] ?? null, $data['name'], $existing?->id, 'partenaire');
        $data['is_visible'] = $request->boolean('is_visible');

        // A new partner goes to the end of its list unless placed.
        $data['position'] = $data['position']
            ?? $existing?->position
            ?? ((int) Partner::max('position') + 1);

        unset($data['logo']);

        return $data;
    }
}
