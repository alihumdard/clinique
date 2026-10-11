@props(['image', 'title'])

<div>
    <x-picture :path="$image" :alt="$title" width="500" height="333" class="aspect-[3/2] w-full rounded-lg object-cover" />
    <h3 class="mt-3 text-sm font-semibold text-slate-900">{{ $title }}</h3>
    <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $slot }}</p>
</div>
