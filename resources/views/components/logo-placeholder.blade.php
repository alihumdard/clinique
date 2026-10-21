{{-- Placeholder logo until the Clinic supplies real artwork for every
     partner: reuses one of the existing logo images (set via $logo) so the
     grid reads as real logos, falling back to an icon or initials when no
     image is given. --}}
@props(['name', 'subtitle' => null, 'initials' => null, 'icon' => null, 'logo' => null, 'size' => 'sm'])

@php
    $initials = $initials ?? collect(explode(' ', $name))->take(2)->map(fn($w) => mb_substr($w, 0, 1))->implode('');
@endphp

@if($size === 'lg')
    <div class="flex h-44 cursor-pointer flex-col items-center justify-center rounded-lg border border-slate-200 bg-white p-6 transition hover:shadow-md">
        @if($logo)
            <img
                src="{{ asset('images/partners/' . $logo . '.png') }}"
                alt="{{ $name }}"
                class="h-28 w-auto max-w-full object-contain"
                loading="lazy"
            >
        @else
            <span class="flex h-14 w-14 items-center justify-center rounded-full bg-primary-50 text-primary-600">
                @if($icon)
                    <x-icon :name="$icon" class="h-7 w-7" />
                @else
                    <span class="text-base font-bold">{{ strtoupper($initials) }}</span>
                @endif
            </span>
        @endif
    </div>
@else
    <div class="flex h-20 flex-col items-center justify-center gap-1 rounded-lg border border-slate-200 bg-white px-4">
        @if($logo)
            <img
                src="{{ asset('images/partners/' . $logo . '.png') }}"
                alt="{{ $name }}"
                class="h-8 w-auto max-w-full object-contain"
                loading="lazy"
            >
        @else
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-50 text-primary-600">
                @if($icon)
                    <x-icon :name="$icon" class="h-4 w-4" />
                @else
                    <span class="text-xs font-bold">{{ strtoupper($initials) }}</span>
                @endif
            </span>
        @endif
        <span class="text-center text-[11px] font-medium leading-tight text-slate-500">{{ $name }}</span>
    </div>
@endif
