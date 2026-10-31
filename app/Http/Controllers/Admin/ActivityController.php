<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ManagesContent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ActivityRequest;
use App\Models\Activity;
use App\Models\ActivityPhoto;
use App\Models\Category;
use App\Services\MediaStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    use ManagesContent;

    public function __construct(private readonly MediaStorage $media)
    {
    }

    public function index(Request $request): View
    {
        $view = $request->query('vue');

        $activities = $this->applyFilters(
            Activity::query()->with('category')->withCount('photos'),
            $request,
            ['title', 'excerpt', 'location']
        )
            // "À venir" and "Passées" are the held_on date, not a status.
            ->when($view === 'a-venir', fn ($q) => $q->upcoming())
            ->when($view === 'passees', fn ($q) => $q->past())
            ->orderByDesc('held_on')
            ->latest('created_at')
            ->paginate($this->perPage($request, 10))
            ->withQueryString();

        return view('admin.activities', [
            'activities' => $activities,
            'categories' => Category::ofType('activity')->withCount('activities')->get(),
            'counts' => $this->publicationCounts(Activity::class) + [
                'upcoming' => Activity::public()->upcoming()->count(),
                'past' => Activity::public()->past()->count(),
                'photos' => ActivityPhoto::count(),
                'all' => Activity::count(),
            ],
            'filters' => $request->only(['statut', 'categorie', 'q', 'vue']),
        ]);
    }

    public function store(ActivityRequest $request): RedirectResponse
    {
        $data = $this->prepare($request);

        if ($request->hasFile('image')) {
            $data['image'] = $this->media->storeImage($request->file('image'), 'activites');
        }

        $activity = Activity::create($data);

        $added = $this->storePhotos($request, $activity);

        return redirect()
            ->route('admin.activities')
            ->with('status', $this->savedMessage($activity, $added, 'enregistrée'));
    }

    public function update(ActivityRequest $request, Activity $activity): RedirectResponse
    {
        $data = $this->prepare($request, $activity);

        $data = $this->applyImage($request, $data, $activity, 'image', 'activites');

        $activity->update($data);

        $added = $this->storePhotos($request, $activity);

        return redirect()
            ->route('admin.activities')
            ->with('status', $this->savedMessage($activity, $added, 'mise à jour'));
    }

    /**
     * A duplicated activity starts with an empty gallery: getAttributes()
     * on the source only ever returns the activities table's own columns,
     * never the related photos rows, so the copy cannot inherit them.
     */
    public function duplicate(Activity $activity): RedirectResponse
    {
        $copy = $this->duplicateAsDraft($activity, Activity::class, 'title', 'activite');

        return redirect()
            ->route('admin.activities')
            ->with('status', "« {$activity->title} » a été dupliquée en brouillon.");
    }

    /** Moves the activity to the corbeille; its photos stay with it. */
    public function destroy(Activity $activity): RedirectResponse
    {
        $activity->delete();

        return redirect()
            ->back(fallback: route('admin.activities'))
            ->with('status', "L'activité « {$activity->title} » a été mise à la corbeille.");
    }

    public function restore(int $id): RedirectResponse
    {
        $activity = Activity::onlyTrashed()->findOrFail($id);
        $activity->restore();

        return redirect()
            ->route('admin.activities')
            ->with('status', "L'activité « {$activity->title} » a été restaurée.");
    }

    /** Permanent: the row, its image and every gallery photo go for good. */
    public function forceDestroy(int $id): RedirectResponse
    {
        $activity = Activity::onlyTrashed()->with('photos')->findOrFail($id);
        $title = $activity->title;

        foreach ($activity->photos as $photo) {
            $this->media->deleteImage($photo->path);
        }

        $this->media->deleteImage($activity->image);

        // The foreign key cascades, so the photo rows go with the activity.
        $activity->forceDelete();

        return redirect()
            ->route('admin.activities')
            ->with('status', "L'activité « {$title} » a été supprimée définitivement.");
    }

    /** One photo out of a gallery, file and row together. */
    public function destroyPhoto(Activity $activity, int $photo): RedirectResponse
    {
        // Scoped to the activity: a photo id from another gallery is a 404,
        // not a deletion.
        $row = $activity->photos()->findOrFail($photo);

        $this->media->deleteImage($row->path);
        $row->delete();

        return back()->with('status', 'La photo a été supprimée.');
    }

    /**
     * @return array<string, mixed>
     */
    private function prepare(ActivityRequest $request, ?Activity $existing = null): array
    {
        $data = $request->validated();

        $data['slug'] = $this->uniqueSlug(Activity::class, $data['slug'] ?? null, $data['title'], $existing?->id, 'activite');
        $data['published_at'] = $this->publicationDate($data, $existing);

        // The uploads are not columns, so they leave the payload here.
        unset($data['photos'], $data['image']);

        return $data;
    }

    /**
     * Appends the submitted files to the gallery, each after the last, so
     * re-ordering stays a matter of position rather than upload order.
     */
    private function storePhotos(ActivityRequest $request, Activity $activity): int
    {
        if (! $request->hasFile('photos')) {
            return 0;
        }

        $position = (int) $activity->photos()->max('position');

        foreach ($request->file('photos') as $file) {
            $activity->photos()->create([
                'path' => $this->media->storeImage($file, 'activites/galerie'),
                'position' => ++$position,
            ]);
        }

        return count($request->file('photos'));
    }

    private function savedMessage(Activity $activity, int $photos, string $verb): string
    {
        $message = "L'activité « {$activity->title} » a été {$verb}.";

        if ($photos > 0) {
            $message .= $photos === 1
                ? ' 1 photo a été ajoutée à la galerie.'
                : " {$photos} photos ont été ajoutées à la galerie.";
        }

        return $message;
    }
}
