{{--
    The partner strip shown at the foot of the home page and of À propos.

    It reads the same rows as the Partenaires page: the list used to be
    written into this file, which is how six Moroccan institutions ended
    up on the home page of a Niger clinic. Whoever includes this partial
    passes $partners.
--}}

@if($partners->isNotEmpty())
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
        @foreach ($partners as $partner)
            <x-logo-placeholder
                size="lg"
                :name="$partner->name"
                :subtitle="$partner->description"
                :logo="$partner->logo"
                :href="$partner->website"
            />
        @endforeach
    </div>
@else
    <p class="rounded-xl border border-slate-200 bg-white p-6 text-center text-sm text-slate-500">
        La liste de nos partenaires sera publiée prochainement.
    </p>
@endif
