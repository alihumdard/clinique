<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(Request $request): View
    {
        $articles = Article::query()
            ->public()
            ->with('category')
            ->when($request->query('categorie'), function ($q, $slug) {
                $q->whereHas('category', fn ($c) => $c->where('slug', $slug));
            })
            ->when($request->query('annee'), fn ($q, $year) => $q->inYear((int) $year))
            ->when($request->query('q'), function ($q, $term) {
                $q->where(function ($inner) use ($term) {
                    $inner->where('title', 'like', "%{$term}%")
                        ->orWhere('excerpt', 'like', "%{$term}%");
                });
            })
            ->latest('published_at')
            ->paginate(6)
            ->withQueryString();

        return view('news.index', [
            'articles' => $articles,
            'categories' => Category::ofType('article')->get(),
            'archives' => Article::archiveYears(),
            'recent' => Article::public()->latest('published_at')->take(4)->get(),
            'filters' => $request->only(['categorie', 'annee', 'q']),
        ]);
    }

    public function show(Article $article): View
    {
        // A draft or a scheduled article is not public yet, whoever has
        // the link. Admins preview from the admin side.
        abort_unless($article->isPublic(), 404);

        $article->increment('views');

        return view('news.show', [
            'article' => $article->load(['category', 'author']),
            'related' => Article::public()
                ->where('id', '!=', $article->id)
                ->when($article->category_id, fn ($q) => $q->where('category_id', $article->category_id))
                ->latest('published_at')
                ->take(3)
                ->get(),
        ]);
    }
}
