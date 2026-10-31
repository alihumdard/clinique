<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ManagesContent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageRequest;
use App\Models\Page;
use App\Services\MediaStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\View\View;

/**
 * Pages the Clinic writes themselves.
 *
 * The design's version of this screen listed the site's own pages —
 * Accueil, Services, Contact, Mentions légales — as if they were editable
 * rows. They are not: each is a designed template with its own sections
 * and its own route, and changing their text is a developer change. Those
 * are shown here as a read-only reference instead, so the Clinic can see
 * what exists without being offered an edit that would do nothing.
 *
 * What this screen does manage is the additional pages: Cookies,
 * Accessibilité, Crédits and anything else the Clinic needs later. Each
 * gets its own address and is served by the catch-all at the end of the
 * route table.
 */
class PageController extends Controller
{
    use ManagesContent;

    public function __construct(private readonly MediaStorage $media)
    {
    }

    public function index(Request $request): View
    {
        $pages = $this->applyFilters(
            Page::query(),
            $request,
            ['title', 'slug', 'body'],
            hasCategory: false
        )
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages', [
            'pages' => $pages,
            'counts' => $this->publicationCounts(Page::class),
            'filters' => $request->only(['statut', 'q']),
            'fixed' => $this->fixedPages(),
        ]);
    }

    public function store(PageRequest $request): RedirectResponse
    {
        $data = $this->prepare($request);

        if ($request->hasFile('image')) {
            $data['image'] = $this->media->storeImage($request->file('image'), 'pages');
        }

        $page = Page::create($data);

        return redirect()
            ->route('admin.pages')
            ->with('status', "La page « {$page->title} » a été créée.");
    }

    public function update(PageRequest $request, Page $page): RedirectResponse
    {
        $data = $this->prepare($request, $page);

        $data = $this->applyImage($request, $data, $page, 'image', 'pages');

        $page->update($data);

        return redirect()
            ->route('admin.pages')
            ->with('status', "La page « {$page->title} » a été mise à jour.");
    }

    /**
     * A copy as a draft, ready to adapt rather than written from scratch.
     *
     * Refused for a system page (abort_if below) even though none ever
     * reaches this controller via the view today — a safety net in case
     * that ever changes, since a system page is a designed template, not
     * CMS content.
     */
    public function duplicate(Page $page): RedirectResponse
    {
        abort_if($page->is_system, 403);

        $copy = $this->duplicateAsDraft($page, Page::class, 'title', 'page');

        return redirect()
            ->route('admin.pages')
            ->with('status', "« {$page->title} » a été dupliquée en brouillon.");
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()
            ->back(fallback: route('admin.pages'))
            ->with('status', "La page « {$page->title} » a été mise à la corbeille.");
    }

    public function restore(int $id): RedirectResponse
    {
        $page = Page::onlyTrashed()->findOrFail($id);
        $page->restore();

        return redirect()
            ->route('admin.pages')
            ->with('status', "La page « {$page->title} » a été restaurée.");
    }

    /** Permanent: the row and its image go for good. */
    public function forceDestroy(int $id): RedirectResponse
    {
        $page = Page::onlyTrashed()->findOrFail($id);
        $title = $page->title;

        $this->media->deleteImage($page->image);
        $page->forceDelete();

        return redirect()
            ->route('admin.pages')
            ->with('status', "La page « {$title} » a été supprimée définitivement.");
    }

    /** @return array<string, mixed> */
    private function prepare(PageRequest $request, ?Page $existing = null): array
    {
        $data = $request->validated();

        $data['slug'] = $this->uniqueSlug(Page::class, $data['slug'], $data['title'], $existing?->id, 'page');
        $data['published_at'] = $this->publicationDate($data, $existing);

        unset($data['image']);

        return $data;
    }

    /**
     * The site's own pages, read from the route table rather than a list
     * kept by hand — a list by hand goes out of date the first time a
     * route is added.
     *
     * @return array<int, array{title: string, url: string}>
     */
    private function fixedPages(): array
    {
        $titles = [
            'home' => 'Accueil',
            'about' => 'À propos',
            'about.team' => 'Équipe',
            'partners' => 'Partenaires',
            'services' => 'Nos services',
            'services.process' => 'Processus de consultation',
            'services.eligibility' => "Conditions d'accès à la gratuité",
            'services.fees' => 'Tarifs administratifs',
            'news.index' => 'Actualités',
            'activities.index' => 'Activités et caravanes',
            'gallery' => 'Galerie photos',
            'resources.guides' => 'Fiches pratiques',
            'resources.reports' => 'Rapports annuels',
            'webtv.index' => 'Web TV',
            'join' => 'Rejoindre la Clinique',
            'contact' => 'Contact',
            'search' => 'Recherche',
            'legal.notice' => 'Mentions légales',
            'legal.terms' => "Conditions d'utilisation",
            'legal.privacy' => 'Politique de confidentialité',
        ];

        $pages = [];

        foreach ($titles as $name => $title) {
            if (RouteFacade::has($name)) {
                $pages[] = ['title' => $title, 'url' => route($name)];
            }
        }

        return $pages;
    }
}
