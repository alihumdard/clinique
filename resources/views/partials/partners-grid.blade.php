<div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">

    @foreach ([
        ['logo' => 'uemf', 'name' => 'UEMF', 'subtitle' => 'Euromed University of Fes Morocco'],
        ['logo' => 'ministere-justice', 'name' => 'Ministère de la Justice', 'subtitle' => 'Royaume du Maroc'],
        ['logo' => 'onu-femmes', 'name' => 'ONU Femmes', 'subtitle' => null],
        ['logo' => 'amdh', 'name' => 'Association Marocaine des Droits Humains', 'subtitle' => null],
        ['logo' => 'barreau-fes', 'name' => 'Barreau de Fès', 'subtitle' => null],
        ['logo' => 'pndd', 'name' => 'PNDD', 'subtitle' => null],
    ] as $partner)
        <div class="flex h-40 cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-4 transition hover:shadow-md">
            <img
                src="{{ asset('images/partners/' . $partner['logo'] . '.png') }}"
                alt="{{ $partner['name'] }}"
                class="h-20 w-auto max-w-full object-contain"
                loading="lazy"
            >
            <span class="text-center text-xs font-semibold leading-tight text-slate-600">
                {{ $partner['name'] }}
            </span>
        </div>
    @endforeach

</div>
