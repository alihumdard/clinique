@props(['icon'])

<div class="flex items-center gap-2.5">
    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-50 text-primary-600">
        <x-icon :name="$icon" class="h-4 w-4" />
    </span>
    <span class="text-sm text-slate-700">{{ $slot }}</span>
</div>
