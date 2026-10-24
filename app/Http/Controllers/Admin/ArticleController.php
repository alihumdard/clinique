<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArticleRequest;
use App\Models\Article;
use App\Models\Category;
use App\Services\MediaStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function __construct(private readonly MediaStorage $media)
    {
    }

    public function index(Request $request): View
    {
        $status = $request->query('statut');

        $articles = Article::query()
            ->with(['category', 'author'])
            // The corbeille is a view of its own, not a filter on the list.
            ->when($status === 'corbeille', fn ($q) => $q->onlyTrashed())
            ->when($status && $status !== 'corbeille', fn ($q) => $q->where('status', $status))
            ->when($request->query('categorie'), fn ($q, $id) => $q->where('category_id', $id))
            ->when($request->query('q'), function ($q, $term) {
                $q->where(function ($inner) use ($term) {
                    $inner->where('title', 'like', "%{$term}%")
                        ->orWhere('excerpt', 'like', "%{$term}%");
                });
            })
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.news', [
            'articles' => $articles,
            // withCount, or the sidebar runs one query per category.
            'categories' => Category::ofType('article')->withCount('articles')->get(),
            'counts' => $this->counts(),
            'filters' => $request->only(['statut', 'categorie', 'q']),
        ]);
    }

    public function store(ArticleRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['title']);
        $data['author_id'] = $request->user()->id;
        $data['published_at'] = $this->publicationDate($data);

        if ($request->hasFile('image')) {
            $data['image'] = $this->media->storeImage($request->file('image'), 'actualites');
        }

        $article = Article::create($data);

        return redirect()
            ->route('admin.news')
            ->with('status', "L'actualité « {$article->title} » a été enregistrée.");
    }

    public function update(ArticleRequest $request, Article $article): RedirectResponse
    {
        $data = $request->validated();

        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['title'], $article->id);
        $data['published_at'] = $this->publicationDate($data, $article);

        if ($request->hasFile('image')) {
            // Replace, then drop the old file — not the other way round, so
            // a failed upload cannot leave the row pointing at nothing.
            $previous = $article->image;
            $data['image'] = $this->media->storeImage($request->file('image'), 'actualites');
            $this->media->deleteImage($previous);
        }

        $article->update($data);

        return redirect()
            ->route('admin.news')
            ->with('status', "L'actualité « {$article->title} » a été mise à jour.");
    }

    /** Moves the article to the corbeille; the file stays until it is purged. */
    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();

        return redirect()
            ->route('admin.news')
            ->with('status', "L'actualité « {$article->title} » a été mise à la corbeille.");
    }

    public function restore(int $id): RedirectResponse
    {
        $article = Article::onlyTrashed()->findOrFail($id);
        $article->restore();

        return redirect()
            ->route('admin.news')
            ->with('status', "L'actualité « {$article->title} » a été restaurée.");
    }

    /** Permanent: the row and its image go for good. */
    public function forceDestroy(int $id): RedirectResponse
    {
        $article = Article::onlyTrashed()->findOrFail($id);
        $title = $article->title;

        $this->media->deleteImage($article->image);
        $article->forceDelete();

        return redirect()
            ->route('admin.news')
            ->with('status', "L'actualité « {$title} » a été supprimée définitivement.");
    }

    /** @return array<string, int> */
    private function counts(): array
    {
        return [
            'published' => Article::public()->count(),
            'drafts' => Article::drafts()->count(),
            'scheduled' => Article::scheduled()->count(),
            'trashed' => Article::onlyTrashed()->count(),
        ];
    }

    /**
     * A published article needs a date: without one it would never appear,
     * since the public scope orders and filters on it.
     */
    private function publicationDate(array $data, ?Article $existing = null): ?string
    {
        if ($data['status'] !== Article::STATUS_PUBLISHED) {
            return $data['published_at'] ?? $existing?->published_at;
        }

        return $data['published_at']
            ?? $existing?->published_at
            ?? now()->toDateTimeString();
    }

    private function uniqueSlug(?string $slug, string $title, ?int $ignore = null): string
    {
        $base = Str::slug($slug ?: $title) ?: 'actualite';
        $candidate = $base;
        $suffix = 2;

        while (Article::withTrashed()
            ->where('slug', $candidate)
            ->when($ignore, fn ($q) => $q->where('id', '!=', $ignore))
            ->exists()
        ) {
            $candidate = "{$base}-{$suffix}";
            $suffix++;
        }

        return $candidate;
    }
}
