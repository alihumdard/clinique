{{-- Placeholder monogram logo until the Clinic supplies real partner logos. --}}
@props(['name', 'initials' => null])

@php
    $initials = $initials ?? collect(explode(' ', $name))->take(2)->map(fn($w) => mb_substr($w, 0, 1))->implode('');
@endphp

<div class="flex h-20 flex-col items-center justify-center gap-1 rounded-lg border border-slate-200 bg-white px-4">
    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-500">
        {{ strtoupper($initials) }}
    </span>
    <span class="text-center text-[11px] font-medium leading-tight text-slate-500">{{ $name }}</span>
</div>
