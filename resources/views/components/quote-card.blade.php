@props(['attribution' => null, 'tint' => false])

<div @class([
    'rounded-xl border border-l-4 border-l-amber-400 p-6',
    'border-primary-100 bg-primary-50/60' => $tint,
    'border-slate-200 bg-white' => !$tint,
])>
    <svg class="h-8 w-8 text-amber-400" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14.017 0v7.391c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z" />
    </svg>
    <p class="mt-4 text-lg font-medium italic leading-snug text-slate-800">
        {{ $slot }}
    </p>
    @if($attribution)
        <p class="mt-4 text-xs text-slate-500">{{ $attribution }}</p>
    @endif
</div>
