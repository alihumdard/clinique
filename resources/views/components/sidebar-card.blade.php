@props(['icon', 'title', 'color' => 'primary', 'tint' => false])

<div @class([
    'rounded-xl border p-5',
    'border-primary-100 bg-primary-50/60' => $tint,
    'border-slate-200 bg-white' => !$tint,
])>
    <div class="flex items-center gap-2.5">
        <x-icon-badge size="sm" @class(['!bg-amber-50 !text-amber-500' => $color === 'amber'])>
            <x-icon :name="$icon" class="h-5 w-5" />
        </x-icon-badge>
        <h3 class="text-base font-bold text-slate-900">{{ $title }}</h3>
    </div>
    <div class="mt-4">
        {{ $slot }}
    </div>
</div>
