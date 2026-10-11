{{-- Placeholder monogram logo until the Clinic supplies real partner logos. --}}
@props(['name', 'subtitle' => null, 'initials' => null, 'size' => 'sm'])

@php
    $initials = $initials ?? collect(explode(' ', $name))->take(2)->map(fn($w) => mb_substr($w, 0, 1))->implode('');
@endphp

@if($size === 'lg')
    <div class="flex flex-col items-center gap-3 rounded-lg border border-slate-200 bg-white p-6 text-center">
        <span class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-base font-bold text-slate-500">
            {{ strtoupper($initials) }}
        </span>
        <div>
            <p class="text-sm font-semibold leading-snug text-slate-900">{{ $name }}</p>
            @if($subtitle)
                <p class="mt-0.5 text-xs text-slate-500">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
@else
    <div class="flex h-20 flex-col items-center justify-center gap-1 rounded-lg border border-slate-200 bg-white px-4">
        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-500">
            {{ strtoupper($initials) }}
        </span>
        <span class="text-center text-[11px] font-medium leading-tight text-slate-500">{{ $name }}</span>
    </div>
@endif
