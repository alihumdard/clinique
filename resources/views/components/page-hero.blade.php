{{--
    Reusable hero banner for inner pages: background photo, dark gradient overlay,
    breadcrumb, title, subtitle. Used by About, Services, News, etc.
--}}
@props([
    'image',
    'title',
    'subtitle' => null,
    'breadcrumb' => [],
])

<section class="relative overflow-hidden">
    <div class="absolute inset-0">
        <x-picture
            :path="$image"
            alt=""
            width="1600" height="500"
            :eager="true"
            class="h-full w-full object-cover"
        />
        <div class="absolute inset-0 bg-gradient-to-r from-navy-950/90 via-navy-900/70 to-navy-900/20"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
        @if(count($breadcrumb))
            <nav aria-label="Fil d'Ariane" class="mb-4 flex items-center gap-2 text-sm text-slate-300">
                @foreach ($breadcrumb as $i => $crumb)
                    @if($i > 0)
                        <span aria-hidden="true">&rsaquo;</span>
                    @endif
                    @if(!empty($crumb['url']) && !$loop->last)
                        <a href="{{ $crumb['url'] }}" class="hover:text-white">{{ $crumb['label'] }}</a>
                    @else
                        <span class="text-white">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </nav>
        @endif

        <h1 class="max-w-2xl text-3xl font-bold leading-tight text-white sm:text-4xl">{{ $title }}</h1>

        @if($subtitle)
            <p class="mt-3 max-w-xl text-base text-slate-200">{{ $subtitle }}</p>
        @endif
    </div>
</section>
