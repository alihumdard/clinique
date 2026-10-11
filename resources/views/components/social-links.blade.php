{{--
    Social profile icons, driven by config/clinique.php. A profile with no
    configured URL is skipped rather than rendered as a dead '#' link.
--}}
@props(['class' => 'bg-white/10 hover:bg-white/20'])

@php
    // Brand glyphs are solid-fill and sit outside the stroke-based x-icon set.
    $marks = [
        'facebook' => ['label' => 'Facebook', 'path' => 'M22 12a10 10 0 10-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.1 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0022 12z'],
        'x' => ['label' => 'X', 'path' => 'M18.9 2H22l-7.4 8.4L23 22h-6.8l-5.3-6.9L4.8 22H1.7l7.9-9L1 2h7l4.8 6.3L18.9 2zm-1.2 18h1.9L7.3 3.9H5.3L17.7 20z'],
        'youtube' => ['label' => 'YouTube', 'path' => 'M23 12s0-3.5-.45-5.17a2.98 2.98 0 00-2.1-2.1C18.78 4.3 12 4.3 12 4.3s-6.78 0-8.45.43a2.98 2.98 0 00-2.1 2.1C1 8.5 1 12 1 12s0 3.5.45 5.17a2.98 2.98 0 002.1 2.1C5.22 19.7 12 19.7 12 19.7s6.78 0 8.45-.43a2.98 2.98 0 002.1-2.1C23 15.5 23 12 23 12zM9.75 15.02V8.98L15.5 12l-5.75 3.02z'],
        'instagram' => ['label' => 'Instagram', 'path' => 'M12 2c2.72 0 3.06.01 4.12.06 1.06.05 1.79.22 2.43.47.66.26 1.21.6 1.76 1.15.5.5.9 1.1 1.15 1.76.25.64.42 1.37.47 2.43C21.99 8.94 22 9.28 22 12s-.01 3.06-.06 4.12c-.05 1.06-.22 1.79-.47 2.43a4.9 4.9 0 01-1.15 1.76 4.9 4.9 0 01-1.76 1.15c-.64.25-1.37.42-2.43.47C15.06 21.99 14.72 22 12 22s-3.06-.01-4.12-.06c-1.06-.05-1.79-.22-2.43-.47a4.9 4.9 0 01-1.76-1.15 4.9 4.9 0 01-1.15-1.76c-.25-.64-.42-1.37-.47-2.43C2.01 15.06 2 14.72 2 12s.01-3.06.06-4.12c.05-1.06.22-1.79.47-2.43.26-.66.6-1.21 1.15-1.76a4.9 4.9 0 011.76-1.15c.64-.25 1.37-.42 2.43-.47C8.94 2.01 9.28 2 12 2zm0 5a5 5 0 100 10 5 5 0 000-10zm0 8.2a3.2 3.2 0 110-6.4 3.2 3.2 0 010 6.4zm5.2-8.4a1.2 1.2 0 100-2.4 1.2 1.2 0 000 2.4z'],
        'linkedin' => ['label' => 'LinkedIn', 'path' => 'M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.03-1.85-3.03-1.85 0-2.14 1.45-2.14 2.94v5.66H9.34V9h3.41v1.56h.05c.47-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.06 2.06 0 110-4.13 2.06 2.06 0 010 4.13zM7.12 20.45H3.56V9h3.56v11.45z'],
    ];

    $profiles = array_filter(config('clinique.social', []));
@endphp

@if(count($profiles))
    <div class="flex items-center gap-3">
        @foreach ($profiles as $key => $url)
            @if(isset($marks[$key]))
                <a
                    href="{{ $url }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="{{ $marks[$key]['label'] }}"
                    class="flex h-9 w-9 items-center justify-center rounded-full {{ $class }}"
                >
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="{{ $marks[$key]['path'] }}" />
                    </svg>
                </a>
            @endif
        @endforeach
    </div>
@endif
