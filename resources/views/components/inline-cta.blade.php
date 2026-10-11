@props(['icon' => 'users', 'title', 'buttonLabel', 'buttonHref' => '#'])

<div class="flex flex-col items-center gap-5 rounded-xl border border-primary-100 bg-primary-50/60 p-6 sm:flex-row sm:justify-between">
    <div class="flex flex-col items-center gap-4 text-center sm:flex-row sm:text-left">
        <x-icon-badge size="lg" class="shrink-0">
            <x-icon :name="$icon" class="h-7 w-7" />
        </x-icon-badge>
        <div>
            <p class="font-semibold text-slate-900">{{ $title }}</p>
            <p class="mt-1 text-sm text-slate-600">{{ $slot }}</p>
        </div>
    </div>
    <x-button :href="$buttonHref" variant="primary" class="shrink-0">{{ $buttonLabel }}</x-button>
</div>
