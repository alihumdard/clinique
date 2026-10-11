@props(['year', 'label', 'accent' => false])

<div class="flex flex-1 flex-col items-center text-center">
    <span class="flex h-5 w-5 items-center justify-center rounded-full {{ $accent ? 'bg-amber-400' : 'bg-primary-600' }} ring-4 ring-white"></span>
    <p class="mt-3 text-base font-bold {{ $accent ? 'text-amber-500' : 'text-primary-700' }}">{{ $year }}</p>
    <p class="mt-1 max-w-[9rem] text-xs leading-snug text-slate-600">{{ $label }}</p>
</div>
