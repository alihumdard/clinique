@props(['icon', 'title', 'color' => 'primary'])

<div class="rounded-xl border border-slate-200 bg-white p-5">
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
