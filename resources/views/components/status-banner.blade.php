{{--
    The bandeau d'état: a notice the Clinic needs every visitor to see —
    a closure, a change of opening hours, an exceptional arrangement.

    Edited from Paramètres du site, and absent from the markup entirely
    when it is switched off, so it costs nothing the rest of the time.
--}}
@php
    $banner = \App\Support\SiteSettings::banner();
@endphp

@if($banner)
    @php
        $tones = [
            'info' => 'bg-primary-600 text-white',
            'warning' => 'bg-amber-500 text-slate-900',
            'closure' => 'bg-rose-600 text-white',
        ];

        $icons = [
            'info' => 'info',
            'warning' => 'help-circle',
            'closure' => 'x-circle',
        ];

        $tone = $tones[$banner['tone']] ?? $tones['info'];
        $icon = $icons[$banner['tone']] ?? 'info';
    @endphp

    {{-- role="status": announced by a screen reader, but not as an
         interruption — it is information, not an error. --}}
    <div role="status" class="{{ $tone }}">
        <div class="mx-auto flex max-w-7xl items-start gap-3 px-4 py-3 sm:px-6 lg:px-8">
            <x-icon :name="$icon" class="mt-0.5 h-5 w-5 shrink-0" />
            <p class="text-sm font-medium leading-relaxed">{{ $banner['message'] }}</p>
        </div>
    </div>
@endif
