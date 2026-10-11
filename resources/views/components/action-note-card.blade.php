@props(['icon', 'title'])

<div class="rounded-xl bg-primary-50/60 p-6">
    <div class="flex items-start gap-4">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-primary-600 text-white">
            <x-icon :name="$icon" class="h-5 w-5" />
        </span>
        <div class="min-w-0 flex-1">
            <p class="font-semibold text-slate-900">{{ $title }}</p>
            <div class="mt-1 text-sm leading-relaxed text-slate-600">{{ $slot }}</div>
        </div>
    </div>
</div>
