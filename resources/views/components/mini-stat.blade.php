@props(['icon', 'value', 'title', 'subtitle'])

<div class="flex items-start gap-3">
    <x-icon-badge size="md" class="shrink-0">
        <x-icon :name="$icon" class="h-7 w-7" />
    </x-icon-badge>
    <div>
        <p class="text-xs text-slate-500">{{ $title }}</p>
        <p class="text-xl font-bold text-slate-900">{{ $value }}</p>
        <p class="text-xs text-slate-500">{{ $subtitle }}</p>
    </div>
</div>
