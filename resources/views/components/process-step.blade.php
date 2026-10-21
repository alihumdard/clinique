@props(['number', 'icon', 'title', 'accent' => false, 'boxed' => false, 'duration' => null])

@if($boxed)
    <div class="flex flex-1 flex-col rounded-lg border border-slate-200 bg-white p-4">
        <div class="flex items-center gap-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-full {{ $accent ? 'bg-amber-400' : 'bg-primary-600' }} text-sm font-bold text-white">
                {{ $number }}
            </span>
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-50 text-primary-600">
                <x-icon :name="$icon" class="h-4 w-4" />
            </span>
        </div>
        <p class="mt-3 font-semibold text-slate-900">{{ $title }}</p>
        <p class="mt-1 flex-1 text-sm leading-snug text-slate-600">{{ $slot }}</p>
        @if($duration)
            <p class="mt-3 flex items-center gap-1.5 text-xs text-slate-500">
                <x-icon name="clock" class="h-3.5 w-3.5" />
                Durée : {{ $duration }}
            </p>
        @endif
    </div>
@else
    <div class="flex items-start gap-4 sm:flex-1 sm:flex-col sm:items-center sm:text-center">
        <div class="flex shrink-0 items-center gap-2">
            <span class="flex h-9 w-9 items-center justify-center rounded-full {{ $accent ? 'bg-amber-400' : 'bg-primary-600' }} text-sm font-bold text-white">
                {{ $number }}
            </span>
            <x-icon :name="$icon" class="h-6 w-6 text-slate-400" />
        </div>
        <div class="sm:mt-3">
            <p class="font-semibold text-slate-900">{{ $title }}</p>
            <p class="mt-1 text-sm leading-snug text-slate-600">{{ $slot }}</p>
        </div>
    </div>
@endif
