@props(['icon' => 'document', 'lead' => null])

<div class="flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-5">
    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-600">
        <x-icon :name="$icon" class="h-5 w-5" />
    </span>
    <p class="text-sm leading-relaxed text-slate-700">
        @if($lead)
            <strong class="font-semibold text-slate-900">{{ $lead }}</strong>
        @endif
        {{ $slot }}
    </p>
</div>
