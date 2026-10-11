{{--
    One numbered clause on a legal page: rule-prefixed heading, optional
    lead paragraph, then either an icon + body or a tinted entity block.
--}}
@props([
    'number',
    'title',
    'lead' => null,
    'icon' => null,
])

<section {{ $attributes->merge(['class' => 'rounded-xl border border-slate-200 bg-white p-6']) }}>
    <x-section-label>{{ $number }}. {{ $title }}</x-section-label>

    @if($lead)
        <p class="mt-3 text-sm text-slate-600">{{ $lead }}</p>
    @endif

    <div class="mt-4 flex items-start gap-4">
        @if($icon)
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary-50 text-primary-600">
                <x-icon :name="$icon" class="h-6 w-6" />
            </span>
        @endif

        <div class="min-w-0 flex-1 text-sm leading-relaxed text-slate-600 [&_a]:font-semibold [&_a]:text-primary-600 hover:[&_a]:text-primary-700">
            {{ $slot }}
        </div>
    </div>
</section>
