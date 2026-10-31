<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ManagesContent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CallRequest;
use App\Models\Call;
use App\Models\Category;
use App\Services\MediaStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CallController extends Controller
{
    use ManagesContent;

    public function __construct(private readonly MediaStorage $media)
    {
    }

    public function index(Request $request): View
    {
        $view = $request->query('vue');

        $calls = $this->applyFilters(
            Call::query()->with('category'),
            $request,
            ['title', 'excerpt']
        )
            // Open and closed are the closing date, not a status. Grouped,
            // or the orWhere would escape the other filters.
            ->when($view === 'ouverts', fn ($q) => $q->where(
                fn ($inner) => $inner->whereNull('closes_on')->orWhereDate('closes_on', '>=', today())
            ))
            ->when($view === 'clotures', fn ($q) => $q->whereDate('closes_on', '<', today()))
            ->orderByDesc('closes_on')
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.calls', [
            'calls' => $calls,
            'categories' => Category::ofType('call')->withCount('calls')->get(),
            'counts' => $this->publicationCounts(Call::class) + [
                'open' => Call::open()->count(),
                'closed' => Call::public()->whereDate('closes_on', '<', today())->count(),
                'all' => Call::count(),
            ],
            'filters' => $request->only(['statut', 'categorie', 'q', 'vue']),
        ]);
    }

    public function store(CallRequest $request): RedirectResponse
    {
        $data = $this->prepare($request);

        if ($request->hasFile('image')) {
            $data['image'] = $this->media->storeImage($request->file('image'), 'appels');
        }

        $call = Call::create($data);

        return redirect()
            ->route('admin.calls')
            ->with('status', "L'appel « {$call->title} » a été enregistré.");
    }

    public function update(CallRequest $request, Call $call): RedirectResponse
    {
        $data = $this->prepare($request, $call);

        $data = $this->applyImage($request, $data, $call, 'image', 'appels');

        $call->update($data);

        return redirect()
            ->route('admin.calls')
            ->with('status', "L'appel « {$call->title} » a été mis à jour.");
    }

    /** A copy as a draft, ready to adapt rather than written from scratch. */
    public function duplicate(Call $call): RedirectResponse
    {
        $copy = $this->duplicateAsDraft($call, Call::class, 'title', 'appel');

        return redirect()
            ->route('admin.calls')
            ->with('status', "« {$call->title} » a été dupliqué en brouillon.");
    }

    public function destroy(Call $call): RedirectResponse
    {
        $call->delete();

        return redirect()
            ->back(fallback: route('admin.calls'))
            ->with('status', "L'appel « {$call->title} » a été mis à la corbeille.");
    }

    public function restore(int $id): RedirectResponse
    {
        $call = Call::onlyTrashed()->findOrFail($id);
        $call->restore();

        return redirect()
            ->route('admin.calls')
            ->with('status', "L'appel « {$call->title} » a été restauré.");
    }

    /** Permanent: the row and its image go for good. */
    public function forceDestroy(int $id): RedirectResponse
    {
        $call = Call::onlyTrashed()->findOrFail($id);
        $title = $call->title;

        $this->media->deleteImage($call->image);
        $call->forceDelete();

        return redirect()
            ->route('admin.calls')
            ->with('status', "L'appel « {$title} » a été supprimé définitivement.");
    }

    /** @return array<string, mixed> */
    private function prepare(CallRequest $request, ?Call $existing = null): array
    {
        $data = $request->validated();

        $data['slug'] = $this->uniqueSlug(Call::class, $data['slug'] ?? null, $data['title'], $existing?->id, 'appel');
        $data['published_at'] = $this->publicationDate($data, $existing);

        unset($data['image']);

        return $data;
    }
}
