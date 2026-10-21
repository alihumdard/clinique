@props(['eyebrow', 'title', 'partners'])

<div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <x-eyebrow>{{ $eyebrow }}</x-eyebrow>
        <h2 class="mt-2 text-2xl font-bold text-slate-900">{{ $title }}</h2>
    </div>
    <x-button :href="route('partners')" variant="text" class="self-start shrink-0 sm:self-auto">Voir tous les partenaires</x-button>
</div>

<div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
    @foreach ($partners as $partner)
        <x-logo-placeholder size="lg" :name="$partner['name']" :subtitle="$partner['subtitle'] ?? null" :icon="$partner['icon'] ?? null" :logo="$partner['logo'] ?? null" />
    @endforeach
</div>

<div class="mt-4 sm:hidden">
    <x-button :href="route('partners')" variant="text">Voir tous les partenaires</x-button>
</div>
