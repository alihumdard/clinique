@props(['icon', 'label'])

<div class="flex items-center gap-2">
    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-50 text-primary-600">
        <x-icon :name="$icon" class="h-4 w-4" />
    </span>
    <span class="text-sm font-medium text-slate-700">{{ $label }}</span>
</div>
