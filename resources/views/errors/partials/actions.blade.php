{{-- The way back to wherever the person was, not to wherever we happen to
     have a link. An error inside the registre must not invite someone to
     the public site as its first suggestion. --}}
@php
    $area = match (true) {
        request()->is('registre', 'registre/*') => ['Retour au registre', route('registre.dashboard')],
        request()->is('admin', 'admin/*') => ['Retour au tableau de bord', route('admin.dashboard')],
        default => null,
    };
@endphp

@if($area)
    <x-button :href="$area[1]" variant="primary">{{ $area[0] }}</x-button>
@endif

<x-button :href="route('home')" variant="outline" :arrow="false">Aller au site public</x-button>
