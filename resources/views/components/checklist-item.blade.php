@props(['icon', 'title'])

<div class="flex items-start gap-3">
    <x-icon-badge size="sm" class="shrink-0">
        <x-icon :name="$icon" class="h-5 w-5" />
    </x-icon-badge>
    <div>
        <p class="text-sm font-semibold text-slate-900">{{ $title }}</p>
        <p class="mt-0.5 text-sm leading-snug text-slate-600">{{ $slot }}</p>
    </div>
</div>
