{{--
    Compact vertical step list with numbered discs joined by a connector
    line. Used in sidebars ("Comment postuler ?").

    $steps: ['title' => '...', 'text' => '...']
--}}
@props(['steps' => []])

<ol {{ $attributes->merge(['class' => 'space-y-5']) }}>
    @foreach ($steps as $i => $step)
        <li class="relative flex gap-3">
            {{-- Connector sits behind the disc and stops at the last step --}}
            @unless($loop->last)
                <span class="absolute left-[0.9375rem] top-8 h-[calc(100%-0.5rem)] w-px bg-primary-100" aria-hidden="true"></span>
            @endunless

            <span class="relative z-10 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-600 text-sm font-bold text-white">
                {{ $i + 1 }}
            </span>

            <div class="min-w-0 pt-0.5">
                <p class="text-sm font-semibold text-slate-900">{{ $step['title'] }}</p>
                <p class="mt-0.5 text-sm leading-snug text-slate-600">{{ $step['text'] }}</p>
            </div>
        </li>
    @endforeach
</ol>
