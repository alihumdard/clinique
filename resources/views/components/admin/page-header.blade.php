@props([
    'title',
    'lead' => null,
    'breadcrumb' => [],
    'actionLabel' => null,
    'actionHref' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between']) }}>
    <div class="min-w-0">
        {{-- Breadcrumb --}}
        @if(count($breadcrumb))
            <nav aria-label="Fil d'Ariane" class="mb-2 flex flex-wrap items-center gap-1.5 text-xs text-slate-500">
                @foreach ($breadcrumb as $crumb)
                    @if(!$loop->first)
                        <svg class="h-3 w-3 shrink-0 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    @endif
                    @if(!empty($crumb['url']) && !$loop->last)
                        <a href="{{ $crumb['url'] }}" class="hover:text-primary-600 hover:underline">{{ $crumb['label'] }}</a>
                    @else
                        <span class="font-medium text-slate-700">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </nav>
        @endif

        <h1 class="text-2xl font-bold text-slate-900 sm:text-3xl">{{ $title }}</h1>

        @if($lead)
            <p class="mt-1.5 text-sm leading-relaxed text-slate-500">{{ $lead }}</p>
        @endif
    </div>

    <div class="flex shrink-0 flex-col items-start gap-3 sm:items-end">
        @if($actionLabel)
            <a
                href="{{ $actionHref ?? '#' }}"
                class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 active:scale-[.98]"
            >
                <x-icon name="plus" class="h-4.5 w-4.5" />
                {{ $actionLabel }}
            </a>
        @endif

        {{ $slot }}
    </div>
</div>
