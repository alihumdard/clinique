@props([
    'image',
    'name',
    'role',
    'bio' => null,
    'email' => null,
    'linkedin' => null,
    'size' => 'compact',
])

@if($size === 'large')
    <div {{ $attributes->merge(['class' => 'flex flex-col rounded-lg border border-slate-200 bg-white overflow-hidden sm:flex-row']) }}>
        <div class="h-56 w-full shrink-0 overflow-hidden sm:h-full sm:w-44">
            <img
                src="{{ asset('images/' . $image . '.jpg') }}"
                alt="{{ $name }}"
                class="h-full w-full object-cover object-top"
                loading="lazy"
                width="176" height="250"
            >
        </div>
        <div class="p-5">
            <p class="text-lg font-bold text-slate-900">{{ $name }}</p>
            <p class="mt-0.5 text-sm font-medium text-primary-600">{{ $role }}</p>
            @if($bio)
                <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $bio }}</p>
            @endif
            <div class="mt-3 flex items-center gap-3">
                @if($email)
                    <a href="mailto:{{ $email }}" class="flex items-center gap-1.5 text-sm text-primary-600 hover:text-primary-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        {{ $email }}
                    </a>
                @endif
                @if($linkedin)
                    <a href="{{ $linkedin }}" target="_blank" rel="noopener" aria-label="LinkedIn de {{ $name }}" class="flex h-7 w-7 items-center justify-center rounded-full bg-primary-50 text-primary-600 hover:bg-primary-100">
                        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.03-1.85-3.03-1.85 0-2.14 1.45-2.14 2.94v5.66H9.34V9h3.41v1.56h.05c.47-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.06 2.06 0 110-4.13 2.06 2.06 0 010 4.13zM7.12 20.45H3.56V9h3.56v11.45z"/></svg>
                    </a>
                @endif
            </div>
        </div>
    </div>
@elseif($size === 'horizontal')
    <div {{ $attributes->merge(['class' => 'flex overflow-hidden rounded-lg border border-slate-200 bg-white']) }}>
        <img
            src="{{ asset('images/' . $image . '.jpg') }}"
            alt="{{ $name }}"
            class="h-40 w-28 shrink-0 object-cover object-top"
            loading="lazy" width="112" height="160"
        >
        <div class="flex min-w-0 flex-col justify-center py-3 pl-3 pr-3">
            <p class="text-sm font-bold text-slate-900">{{ $name }}</p>
            <p class="text-xs font-medium text-primary-600">{{ $role }}</p>
            @if($bio)
                <p class="mt-1 text-xs leading-snug text-slate-500">{{ $bio }}</p>
            @endif
            @if($email)
                <a href="mailto:{{ $email }}" aria-label="Contacter {{ $name }}" class="mt-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-primary-50 text-primary-600 hover:bg-primary-100">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                </a>
            @endif
        </div>
    </div>
@else
    <div {{ $attributes->merge(['class' => 'overflow-hidden rounded-lg border border-slate-200 bg-white']) }}>
        <img
            src="{{ asset('images/' . $image . '.jpg') }}"
            alt="{{ $name }}"
            class="w-full object-cover object-top"
            style="aspect-ratio: 5/6;"
            loading="lazy" width="200" height="240"
        >
        <div class="px-3 py-2.5">
            <p class="text-sm font-semibold leading-snug text-slate-900">{{ $name }}</p>
            <p class="mt-0.5 text-xs font-medium text-primary-600">{{ $role }}</p>
            @if($bio)
                <p class="mt-1 text-xs leading-snug text-slate-400">{{ $bio }}</p>
            @endif
        </div>
    </div>
@endif
