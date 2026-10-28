@extends('layouts.app')

@section('title', ($page->seo_title ?: $page->title) . ' - ' . config('app.name'))
@section('meta_description', $page->seo_description ?? \Illuminate\Support\Str::limit(strip_tags($page->body ?? ''), 150))

@section('content')

    <x-page-hero
        :image="$page->image ? null : 'placeholders/legal-scales-gavel'"
        :image-url="$page->image ? $page->imageUrl($page->image, 'placeholders/legal-scales-gavel') : null"
        :title="$page->title"
        :breadcrumb="[
            ['label' => __('Accueil'), 'url' => route('home')],
            ['label' => $page->title],
        ]"
    />

    <section class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
        {{--
            whitespace-pre-line keeps the editor's paragraph breaks without
            trusting their markup: the body is escaped, so a page cannot put
            script or styling on the site.
        --}}
        <div class="whitespace-pre-line text-base leading-relaxed text-slate-700">
            {{ $page->body }}
        </div>

        <p class="mt-10 border-t border-slate-100 pt-5 text-xs text-slate-400">
            Dernière mise à jour : {{ $page->updated_at->translatedFormat('j F Y') }}
        </p>
    </section>

@endsection
