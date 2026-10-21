@props(['icon', 'value', 'title', 'subtitle'])

<div class="flex items-start gap-2.5 sm:gap-3">
    <x-icon-badge size="md" class="!h-10 !w-10 shrink-0 sm:!h-12 sm:!w-12">
        <x-icon :name="$icon" class="h-5 w-5 sm:h-7 sm:w-7" />
    </x-icon-badge>
    <div class="min-w-0">
        <p class="text-xs leading-snug text-slate-500">{{ $title }}{{ $subtitle ? ' ' . $subtitle : '' }}</p>
        <p class="text-lg font-bold text-slate-900 sm:text-xl">{{ $value }}</p>
    </div>
</div>
