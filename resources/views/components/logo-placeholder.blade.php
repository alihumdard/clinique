{{--
    A partner's logo in the grid.

    `logoUrl` is a stored upload; `logo` names one of the bundled artwork
    files under public/images/partners, which the static pages still use.
    With neither, the tile falls back to an icon or the partner's initials,
    so a partner the Clinic has not yet sent artwork for still reads as a
    real entry rather than a blank box.
--}}
@props([
    'name',
    'subtitle' => null,
    'initials' => null,
    'icon' => null,
    'logo' => null,
    'logoUrl' => null,
    'href' => null,
    'size' => 'sm',
])

@php
    $initials = $initials ?? collect(explode(' ', $name))->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->implode('');
    $src = $logoUrl ?? ($logo ? asset('images/partners/'.$logo.'.png') : null);

    // A partner's own site opens in a new tab; noopener, because the target
    // is an address the Clinic typed in rather than one we control.
    $tag = $href ? 'a' : 'div';
@endphp

@if($size === 'lg')
    <{{ $tag }}
        @if($href) href="{{ $href }}" target="_blank" rel="noopener noreferrer" @endif
        class="flex h-44 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-6 text-center transition hover:shadow-md {{ $href ? 'cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600' : '' }}"
    >
        @if($src)
            <img
                src="{{ $src }}"
                alt="{{ $name }}"
                class="h-24 w-auto max-w-full object-contain"
                loading="lazy"
            >
        @else
            <span class="flex h-14 w-14 items-center justify-center rounded-full bg-primary-50 text-primary-600">
                @if($icon)
                    <x-icon :name="$icon" class="h-7 w-7" />
                @else
                    <span class="text-base font-bold">{{ mb_strtoupper($initials) }}</span>
                @endif
            </span>

            <span class="text-sm font-semibold leading-tight text-slate-800">{{ $name }}</span>

            @if($subtitle)
                <span class="line-clamp-2 text-xs leading-tight text-slate-500">{{ $subtitle }}</span>
            @endif
        @endif
    </{{ $tag }}>
@else
    <{{ $tag }}
        @if($href) href="{{ $href }}" target="_blank" rel="noopener noreferrer" @endif
        class="flex h-20 flex-col items-center justify-center gap-1 rounded-lg border border-slate-200 bg-white px-4"
    >
        @if($src)
            <img
                src="{{ $src }}"
                alt="{{ $name }}"
                class="h-8 w-auto max-w-full object-contain"
                loading="lazy"
            >
        @else
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-50 text-primary-600">
                @if($icon)
                    <x-icon :name="$icon" class="h-4 w-4" />
                @else
                    <span class="text-xs font-bold">{{ mb_strtoupper($initials) }}</span>
                @endif
            </span>
        @endif

        <span class="text-center text-[11px] font-medium leading-tight text-slate-500">{{ $name }}</span>
    </{{ $tag }}>
@endif
