@props(['icon', 'title', 'color' => 'primary', 'tint' => false])

<div @class([
    'rounded-xl border p-5',
    'border-primary-100 bg-primary-50/60' => $tint,
    'border-slate-200 bg-white' => !$tint,
])>
    <div class="flex items-center gap-2.5">
        <x-icon-badge size="sm" @class(['!bg-amber-50 !text-amber-500' => $color === 'amber', '!bg-rose-50 !text-rose-600' => $icon === 'youtube'])>
            @if($icon === 'youtube')
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M23.5 6.2a3.02 3.02 0 00-2.12-2.14C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.38.56A3.02 3.02 0 00.5 6.2 31.6 31.6 0 000 12a31.6 31.6 0 00.5 5.8 3.02 3.02 0 002.12 2.14c1.88.56 9.38.56 9.38.56s7.5 0 9.38-.56a3.02 3.02 0 002.12-2.14A31.6 31.6 0 0024 12a31.6 31.6 0 00-.5-5.8zM9.75 15.5v-7l6.5 3.5-6.5 3.5z"/></svg>
            @else
                <x-icon :name="$icon" class="h-5 w-5" />
            @endif
        </x-icon-badge>
        <h3 class="text-base font-bold text-slate-900">{{ $title }}</h3>
    </div>
    <div class="mt-4">
        {{ $slot }}
    </div>
</div>
