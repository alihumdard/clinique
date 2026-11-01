{{--
    The status column, as a control rather than a label.

    Publishing used to mean opening the edit dialog, finding the field
    among a dozen others and saving the whole form for one column. The
    listing already showed the status here; it now changes it here.

    The badge is kept where the row cannot take a status straight from
    the table:

      — a row in the corbeille, which offers Restaurer and nothing else;
      — "Programmée", a published row whose date is still ahead, and
        "Masqué", a published row hidden on its own: neither is a status,
        both are what statusLabel() reports about one, and changing them
        here would quietly drop the date or the visibility that caused it.

    No JavaScript beyond the submit: the select posts its own form, so the
    change survives with scripts off, and the page comes back by back(),
    which the scroll restoration returns to the row.
--}}
@props([
    'row',
    'action',
    'label' => null,
])

@php
    $tones = [
        'green' => 'border-emerald-200 bg-emerald-50 text-emerald-700 focus:border-emerald-400 focus:ring-emerald-300',
        'amber' => 'border-amber-200 bg-amber-50 text-amber-700 focus:border-amber-400 focus:ring-amber-300',
        'slate' => 'border-slate-200 bg-slate-50 text-slate-600 focus:border-slate-400 focus:ring-slate-300',
        'red'   => 'border-rose-200 bg-rose-50 text-rose-700 focus:border-rose-400 focus:ring-rose-300',
        'blue'  => 'border-primary-200 bg-primary-50 text-primary-700 focus:border-primary-400 focus:ring-primary-300',
    ];

    $current = $row->statusLabel();

    // Only the three the Publishable trait declares. The other labels are
    // reports about a published row, not statuses it can be set to.
    $settable = ! $row->trashed() && in_array($current, ['Publié', 'Brouillon', 'Archivé'], true);

    $tone = $tones[$row->statusTone()] ?? $tones['slate'];
@endphp

@if($settable)
    <form method="POST" action="{{ $action }}" class="inline-flex">
        @csrf
        <label for="statut-{{ $row->getKey() }}-{{ $row->getTable() }}" class="sr-only">
            Statut{{ $label ? ' : '.$label : '' }}
        </label>
        <select
            id="statut-{{ $row->getKey() }}-{{ $row->getTable() }}"
            name="status"
            data-submit-on-change
            class="rounded-full border py-1 pl-2.5 pr-7 text-xs font-semibold transition focus:outline-none focus:ring-1 {{ $tone }}"
        >
            <option value="published" @selected($row->status === 'published')>Publié</option>
            <option value="draft" @selected($row->status === 'draft')>Brouillon</option>
            <option value="archived" @selected($row->status === 'archived')>Archivé</option>
        </select>

        {{-- Without JavaScript the select still needs a way to submit. --}}
        <noscript>
            <button type="submit" class="ml-1.5 rounded-md border border-slate-200 px-2 py-1 text-xs text-slate-600">OK</button>
        </noscript>
    </form>
@else
    <x-admin.badge :tone="$row->statusTone()">{{ $current }}</x-admin.badge>
@endif
