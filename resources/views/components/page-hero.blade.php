{{--
    Reusable hero banner for inner pages: background photo, dark gradient overlay,
    breadcrumb, title, subtitle. Used by About, Services, News, etc.
--}}
@props([
    'image',
    'title',
    'subtitle' => null,
    'breadcrumb' => [],
    'badge' => null,
    'badgeColor' => 'primary',
    'titleLine2' => null,
    'imagePosition' => 'object-left',
])

<section class="relative overflow-hidden">
    <div class="absolute inset-0">
        <x-picture
            :path="$image"
            alt=""
            width="2170" height="725"
            :eager="true"
            class="h-full w-full object-cover {{ $imagePosition }}"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-navy-950/85 via-navy-900/30 to-transparent sm:bg-gradient-to-r sm:from-navy-950/70 sm:via-navy-900/35 sm:to-transparent"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
        @if(count($breadcrumb))
            <nav aria-label="Fil d'Ariane" class="mb-4 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-slate-300">
                @foreach ($breadcrumb as $i => $crumb)
                    @if($i > 0)
                        <span aria-hidden="true" class="shrink-0">&rsaquo;</span>
                    @endif
                    @if(!empty($crumb['url']) && !$loop->last)
                        <a href="{{ $crumb['url'] }}" class="shrink-0 whitespace-nowrap hover:text-white">{{ $crumb['label'] }}</a>
                    @else
                        <span class="whitespace-nowrap text-white">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </nav>
        @endif

        @if($badge)
            <x-category-badge :color="$badgeColor" position="inline" class="mb-4">{{ $badge }}</x-category-badge>
        @endif

        <h1 class="max-w-2xl text-3xl font-bold leading-tight text-white sm:text-4xl">
            {{ $title }}
            @if($titleLine2)
                <span class="mt-1 block text-2xl font-bold sm:text-3xl">{{ $titleLine2 }}</span>
            @endif
        </h1>

        @if($subtitle)
            <p class="mt-3 max-w-xl text-base text-slate-200">{{ $subtitle }}</p>
        @endif
    </div>
</section>
