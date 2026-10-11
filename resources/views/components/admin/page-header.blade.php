{{--
    Admin screen header: title, lead sentence, optional breadcrumb and a
    primary action. Repeats on every management screen.
--}}
@props([
    'title',
    'lead' => null,
    'breadcrumb' => [],
    'actionLabel' => null,
    'actionHref' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between']) }}>
    <div class="min-w-0">
        <h1 class="text-2xl font-bold text-navy-900 sm:text-3xl">{{ $title }}</h1>

        @if($lead)
            <p class="mt-1.5 text-sm text-slate-600">{{ $lead }}</p>
        @endif
    </div>

    <div class="flex shrink-0 flex-col items-start gap-3 sm:items-end">
        @if(count($breadcrumb))
            <nav aria-label="Fil d'Ariane" class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
                @foreach ($breadcrumb as $crumb)
                    @if(!$loop->first)
                        <span aria-hidden="true">&rsaquo;</span>
                    @endif

                    @if(!empty($crumb['url']) && !$loop->last)
                        <a href="{{ $crumb['url'] }}" class="hover:text-primary-600">{{ $crumb['label'] }}</a>
                    @else
                        <span class="text-slate-700">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </nav>
        @endif

        @if($actionLabel)
            <a
                href="{{ $actionHref ?? '#' }}"
                class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-primary-700"
            >
                <x-icon name="plus" class="h-5 w-5" />
                {{ $actionLabel }}
            </a>
        @endif

        {{ $slot }}
    </div>
</div>
