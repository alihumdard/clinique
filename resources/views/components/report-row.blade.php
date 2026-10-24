{{--
    Horizontal document row: cover thumbnail on the left, then title,
    summary, a meta strip (date / format / views) and a download action.
    Stacks vertically on phones so the cover never squeezes the text.
--}}
@props([
    // `cover` is a placeholder path under public/images; `coverUrl` is a
    // ready URL, which is what a stored cover gives. One or the other.
    'cover' => null,
    'coverUrl' => null,
    'title',
    'excerpt' => null,
    'date' => null,
    'filesize' => null,
    'views' => null,
    'href' => '#',
])

<article class="flex flex-col overflow-hidden rounded-lg border border-slate-200 bg-white sm:flex-row">
    @if($coverUrl)
        <img
            src="{{ $coverUrl }}"
            alt="Couverture : {{ $title }}"
            width="400" height="520"
            loading="lazy"
            class="aspect-[10/13] w-full shrink-0 object-cover sm:aspect-auto sm:h-auto sm:w-40"
        >
    @else
        <x-picture
            :path="$cover"
            :alt="'Couverture : ' . $title"
            width="400" height="520"
            class="aspect-[10/13] w-full shrink-0 object-cover sm:aspect-auto sm:h-auto sm:w-40"
        />
    @endif

    <div class="flex flex-1 flex-col p-5">
        <h3 class="text-lg font-bold leading-snug text-slate-900">
            <a href="{{ $href }}" class="hover:text-primary-600">{{ $title }}</a>
        </h3>

        @if($excerpt)
            <p class="mt-1.5 text-sm leading-relaxed text-slate-600">{{ $excerpt }}</p>
        @endif

        <div class="mt-auto flex flex-col gap-3 pt-4 sm:flex-row sm:flex-wrap sm:items-center">
            <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                @if($date)
                    <span class="inline-flex items-center gap-1.5 text-xs text-slate-500">
                        <x-icon name="calendar" class="h-4 w-4 shrink-0" />
                        {{ $date }}
                    </span>
                @endif

                @if($filesize)
                    <span class="inline-flex items-center gap-1.5 text-xs text-slate-500">
                        <x-icon name="document" class="h-4 w-4 shrink-0 text-rose-500" />
                        PDF ({{ $filesize }})
                    </span>
                @endif

                @if($views)
                    <span class="inline-flex items-center gap-1.5 text-xs text-slate-500">
                        <x-icon name="eye" class="h-4 w-4 shrink-0" />
                        {{ $views }} vues
                    </span>
                @endif
            </div>

            <a
                href="{{ $href }}"
                class="flex w-full items-center justify-center gap-1.5 rounded-md border border-primary-200 bg-primary-50 px-3.5 py-2 text-xs font-semibold text-primary-700 transition-colors hover:bg-primary-100 sm:ml-auto sm:w-auto"
            >
                <x-icon name="download" class="h-4 w-4" />
                Télécharger
            </a>
        </div>
    </div>
</article>
