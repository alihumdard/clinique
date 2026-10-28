{{--
    The body of an error page: what happened, in French, and the way out.

    The status code is shown because it is the one thing worth quoting when
    someone reports the screen, and the message comes from the exception
    where the application set one — abort(410, '...') already says the
    right thing in French, and it was being thrown away.
--}}
@props(['status', 'title', 'message', 'icon' => 'info'])

<x-icon-badge class="mt-8"><x-icon :name="$icon" class="h-6 w-6" /></x-icon-badge>

<h1 class="mt-6 text-2xl font-bold text-slate-900 sm:text-3xl">{{ $title }}</h1>

<p class="mt-3 text-base leading-relaxed text-slate-600">{{ $message }}</p>

<p class="mt-4 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
    Erreur {{ $status }}
</p>

<div class="mt-8 flex flex-wrap items-center justify-center gap-3">
    {{ $slot }}
</div>
