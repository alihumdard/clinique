{{--
    Horizontal "appel a candidatures" row: thumbnail, type badge, title,
    summary, publication/closing dates, and a details action.
    Stacks on phones so the thumbnail never crushes the text.
--}}
@props([
    'image',
    'type',
    'typeColor' => 'primary',
    'title',
    'excerpt' => null,
    'published' => null,
    'deadline' => null,
    'href' => '#',
])

<article class="flex flex-col overflow-hidden rounded-lg border border-slate-200 bg-white sm:flex-row">
    <x-picture
        :path="$image"
        :alt="$title"
        width="400" height="300"
        class="h-44 w-full shrink-0 object-cover sm:h-auto sm:w-56"
    />

    <div class="flex flex-1 flex-col p-5">
        <x-category-badge :color="$typeColor" position="inline" class="self-start">{{ $type }}</x-category-badge>

        <h3 class="mt-2.5 text-base font-bold leading-snug text-slate-900">
            <a href="{{ $href }}" class="hover:text-primary-600">{{ $title }}</a>
        </h3>

        @if($excerpt)
            <p class="mt-1.5 text-sm leading-relaxed text-slate-600">{{ $excerpt }}</p>
        @endif

        <div class="mt-auto flex flex-wrap items-center gap-x-5 gap-y-2 pt-4">
            @if($published)
                <span class="inline-flex items-center gap-1.5 text-xs text-slate-500">
                    <x-icon name="calendar" class="h-4 w-4 shrink-0" />
                    Publié le {{ $published }}
                </span>
            @endif

            @if($deadline)
                {{-- Deadline carries a warning tone: it is the actionable date --}}
                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-rose-600">
                    <x-icon name="clock" class="h-4 w-4 shrink-0" />
                    Date limite : {{ $deadline }}
                </span>
            @endif

            <x-button :href="$href" variant="outline" class="ml-auto shrink-0 !px-4 !py-2 !text-xs">
                Voir les détails
            </x-button>
        </div>
    </div>
</article>
