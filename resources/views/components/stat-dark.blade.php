@props(['icon', 'value', 'label'])

<div class="rounded-lg border border-white/10 bg-white/5 p-6 text-center">
    <x-icon-badge class="mx-auto !bg-white/10 !text-white">
        <x-icon :name="$icon" class="h-6 w-6" />
    </x-icon-badge>
    <p class="mt-4 text-3xl font-bold text-white">{{ $value }}</p>
    <p class="mt-1 text-sm text-slate-300">{{ $label }}</p>
</div>
