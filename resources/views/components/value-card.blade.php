@props(['icon', 'title'])

<div class="rounded-lg border border-slate-200 bg-white p-6 text-center">
    <x-icon-badge class="mx-auto !bg-primary-600 !text-white">
        <x-icon :name="$icon" class="h-6 w-6" />
    </x-icon-badge>
    <h3 class="mt-4 text-base font-semibold text-slate-900">{{ $title }}</h3>
    <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $slot }}</p>
</div>
