@props(['number', 'icon', 'title', 'accent' => false])

<div class="flex items-start gap-4 sm:flex-1 sm:flex-col sm:items-center sm:text-center">
    <div class="flex shrink-0 flex-col items-center sm:flex-row sm:gap-2">
        <span class="flex h-9 w-9 items-center justify-center rounded-full {{ $accent ? 'bg-amber-400' : 'bg-primary-600' }} text-sm font-bold text-white">
            {{ $number }}
        </span>
        <x-icon :name="$icon" class="mt-2 h-6 w-6 text-slate-400 sm:mt-0" />
    </div>
    <div class="sm:mt-3">
        <p class="font-semibold text-slate-900">{{ $title }}</p>
        <p class="mt-1 text-sm leading-snug text-slate-600">{{ $slot }}</p>
    </div>
</div>
