<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ManagesContent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArticleRequest;
use App\Models\Article;
use App\Models\Category;
use App\Services\MediaStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    use ManagesContent;

    public function __construct(private readonly MediaStorage $media)
    {
    }

    public function index(Request $request): View
    {
        $articles = $this->applyFilters(
            Article::query()->with(['category', 'author']),
            $request,
            ['title', 'excerpt']
        )
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.news', [
            'articles' => $articles,
            // withCount, or the sidebar runs one query per category.
            'categories' => Category::ofType('article')->withCount('articles')->get(),
            'counts' => $this->publicationCounts(Article::class),
            'filters' => $request->only(['statut', 'categorie', 'q']),
        ]);
    }

    public function store(ArticleRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['slug'] = $this->uniqueSlug(Article::class, $data['slug'] ?? null, $data['title'], null, 'actualite');
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

        $data['slug'] = $this->uniqueSlug(Article::class, $data['slug'] ?? null, $data['title'], $article->id, 'actualite');
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
}
