<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ManagesContent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TeamMemberRequest;
use App\Models\TeamMember;
use App\Services\MediaStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends Controller
{
    use ManagesContent;

    public function __construct(private readonly MediaStorage $media)
    {
    }

    public function index(Request $request): View
    {
        $members = $this->applyFilters(
            TeamMember::query(),
            $request,
            ['name', 'role', 'bio'],
            hasCategory: false
        )
            ->when($request->query('groupe'), fn ($q, $group) => $q->where('department', $group))
            // Newest member first in this list: position is the public
            // team page's display order, not an admin browsing order, so
            // a row just created would otherwise land wherever its
            // (usually default) position happens to sort it.
            ->latest('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('admin.team', [
            'members' => $members,
            'departments' => TeamMemberRequest::DEPARTMENTS,
            // One grouped query rather than one count per group.
            'byDepartment' => TeamMember::query()
                ->where('status', TeamMember::STATUS_PUBLISHED)
                ->where('is_visible', true)
                ->groupBy('department')
                ->selectRaw('department, COUNT(*) as total')
                ->pluck('total', 'department'),
            'counts' => $this->listCounts(TeamMember::class),
            'filters' => $request->only(['statut', 'q', 'groupe']),
        ]);
    }

    public function store(TeamMemberRequest $request): RedirectResponse
    {
        $data = $this->prepare($request);

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->media->storeImage($request->file('photo'), 'equipe');
        }

        $member = TeamMember::create($data);

        return redirect()
            ->route('admin.team')
            ->with('status', "{$member->name} a été ajouté à l'équipe.");
    }

    public function update(TeamMemberRequest $request, TeamMember $member): RedirectResponse
    {
        $data = $this->prepare($request, $member);

        if ($request->hasFile('photo')) {
            // Replace, then drop the old file — not the other way round, so
            // a failed upload cannot leave the row pointing at nothing.
            $previous = $member->photo;
            $data['photo'] = $this->media->storeImage($request->file('photo'), 'equipe');
            $this->media->deleteImage($previous);
        }

        $member->update($data);

        return redirect()
            ->route('admin.team')
            ->with('status', "La fiche de {$member->name} a été mise à jour.");
    }

    public function duplicate(TeamMember $member): RedirectResponse
    {
        $copy = $this->duplicateAsDraft($member, TeamMember::class, 'name', 'membre');

        return redirect()
            ->route('admin.team')
            ->with('status', "{$member->name} a été dupliqué(e) en brouillon.");
    }

    public function destroy(TeamMember $member): RedirectResponse
    {
        $member->delete();

        return redirect()
            ->route('admin.team')
            ->with('status', "La fiche de {$member->name} a été mise à la corbeille.");
    }

    public function restore(int $id): RedirectResponse
    {
        $member = TeamMember::onlyTrashed()->findOrFail($id);
        $member->restore();

        return redirect()
            ->route('admin.team')
            ->with('status', "La fiche de {$member->name} a été restaurée.");
    }

    /**
     * Permanent: the row, which carries personal data, and the photo both
     * go for good. This is the deletion the retention rules ask for.
     */
    public function forceDestroy(int $id): RedirectResponse
    {
        $member = TeamMember::onlyTrashed()->findOrFail($id);
        $name = $member->name;

        $this->media->deleteImage($member->photo);
        $member->forceDelete();

        return redirect()
            ->route('admin.team')
            ->with('status', "La fiche de {$name} a été supprimée définitivement.");
    }

    /**
     * Shows or hides one member. A member who leaves is hidden rather than
     * left on the public page, which is what the plan asks for.
     */
    public function toggle(TeamMember $member): RedirectResponse
    {
        $member->update(['is_visible' => ! $member->is_visible]);

        return back()->with('status', $member->is_visible
            ? "{$member->name} est désormais visible sur le site."
            : "{$member->name} est désormais masqué sur le site.");
    }

    /** @return array<string, mixed> */
    private function prepare(TeamMemberRequest $request, ?TeamMember $existing = null): array
    {
        $data = $request->validated();

        $data['slug'] = $this->uniqueSlug(TeamMember::class, $data['slug'] ?? null, $data['name'], $existing?->id, 'membre');
        $data['is_visible'] = $request->boolean('is_visible');

        // A new member goes to the end of their group unless placed.
        $data['position'] = $data['position']
            ?? $existing?->position
            ?? ((int) TeamMember::where('department', $data['department'])->max('position') + 1);

        unset($data['photo']);

        return $data;
    }
}
