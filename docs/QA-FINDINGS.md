# QA findings

## Pass 1 — public site, browser only — 28 October 2026

Environment: `https://clinique.40-160-97-168.sslip.io` · commit `572411b`
Tested by: a browser agent driving Chrome, from outside the server, with
no access to the repository, the database or the logs.

### Coverage

| Scope | State |
| --- | --- |
| A. Public site, links, images | Done — 132 URLs, 118 HTML pages, 75 distinct routes |
| B. Public forms, pré-saisine | Not started |
| C. Administration CRUD | Not started |
| D. Rôles / éditeur | Not started |
| E. Registre, separation tests | Not started |
| F. Responsive, keyboard, console | Partial — server-side metrics only |

About a third of the plan. Nothing behind a sign-in was opened, so this
pass says nothing about the CMS, the roles, or the separation of the
registre — which is the highest-risk part of the platform.

Two things stopped it. The admin password was not supplied, and the agent
declined to type credentials on a public host that the client is using,
which is the right call and not something to work around. The protocol is
now written into `QA-PROMPT.md`: the human signs in, the agent drives
every screen afterwards.

### What held up

No defect found in any of this:

- **0 broken internal links** across 132 URLs. No 404, no 500. One
  deliberate redirect.
- **904 images, 0 missing alt.** The 345 empty `alt=""` are card
  thumbnails whose heading link beside them carries the text — correct
  practice, and correctly identified as such.
- Responsive images are properly built: `<picture>` with a WebP source and
  a JPEG fallback, `srcset` with width descriptors, correct `sizes`.
- One `<h1>` per page, on all 118 pages. Every form input carries a label
  or an `aria-label`.
- CLS 0. gzip on. HTML compresses 131 Ko → 16 Ko. Home page 327 Ko
  against a 500 Ko budget.
- Canonical, meta description, Open Graph and viewport present everywhere.

### Findings, and what was done

| | Finding | Severity | Verdict |
| --- | --- | --- | --- |
| 1 | Server response time: median TTFB 990 ms, p90 2.47 s, 5.70 s on `/services/processus-de-consultation` | Majeur | **Misattributed — no defect** |
| 2 | Paginated listings canonicalise to page 1 | Mineur | Confirmed · fixed |
| 3 | `<title>` identical across filter and pagination variants | Mineur | Confirmed · fixed |
| 4 | No `hreflang` alternates | Mineur | Confirmed · **deliberately not fixed** |
| 5 | Heading order skips H2 | Mineur | Confirmed · fixed |
| 6 | Two `Cache-Control` headers on static assets | Mineur | Confirmed · fixed |
| 7 | 15 images without `width`/`height` | Remarque | Partly confirmed · one fixed |
| 8 | Static images taking 7–10 s; one never completed | — | Retracted by the reporter |
| 9 | `hero-fsjp.jpg` weighs 199 Ko | — | Retracted by the reporter |
| 10 | Two pages share one title ("Session de sensibilisation des jeunes") | Remarque | Seeded demo content, not a defect |
| 11 | `rapports annuels` have no detail page | — | Intended |

#### 1 — Server response time: not an application defect

The numbers are real; the attribution was not. Measured on the server
itself, over TLS on the loopback, three runs each:

| | |
| --- | --- |
| `/` | 42 ms |
| `/actualites` | 42–52 ms |
| `/services/processus-de-consultation` | **29 ms** |
| `/activites` | 40–43 ms |
| `/galerie` | 40–45 ms |
| `/recherche` | 30–33 ms |

The page reported as the slowest at 5.70 s is in fact the fastest the
application serves. `APP_DEBUG` is false, `APP_ENV` is production, the
config and route caches are present, OPcache is on with 128 Mo.

What was measured was the network path. The reporter's own figures say so
before PHP is reached: a TCP connect of 923 ms and a TLS handshake of
516 ms are both complete before the request is sent. Latency for a visitor
in Niamey is a hosting question for UAM — where the machine sits, and
whether a CDN fronts it — not a line of code.

Kept here because the lesson generalises, and it is now a section of both
QA documents: separate what you measured from what you concluded, and say
where you measured from.

#### 2, 3 — Canonical and title on listing variants

`url()->current()` drops the query string, so `/actualites?page=2`
declared `/actualites` as its canonical — an instruction to drop page two
from the index. The same for every rubrique filter. And the title did not
vary, so fifteen URLs were one document called "Actualités".

`App\Support\Seo::canonical()` now keeps the parameters that change which
items a page shows, drops campaign parameters (`utm_*`, `fbclid` and the
rest) that only record where a visitor came from, and sorts what remains
so that two orderings of the same filters are one address. The layout
appends the page number to the title from page two onwards, and the four
listings that lacked it — actualités, activités, galerie, rejoindre — now
name the active rubrique.

#### 4 — hreflang: deliberately not added

Correct as a finding, wrong as a fix. The English version is an interface
translation served from `lang/en.json` at the same URL, chosen by cookie,
and the body text of every page stays French until the Clinic translates
it. Alternates would advertise two addresses that are one address, holding
pages that are near-identical. It becomes the right thing to do the day
the content itself is translated and each language has its own URL. The
reasoning is recorded in `App\Support\Seo` so it is not re-litigated.

What *was* wrong underneath, and was not reported: the document declared
`lang="fr"` and `og:locale` `fr_FR` whatever the chosen language. A page
served in English was telling screen readers and search engines it was
French. Both now follow the active locale.

#### 5 — Heading order

Two pages went from `h1` to `h3`. `/actualites` has no section heading
above its article grid in the design, so the cards' `h3` followed the page
title directly; it now carries a visually hidden `h2` naming the list,
which also gives a screen reader something the filter pills only imply.
On `/politique-confidentialite` the sidebar prompt comes first in document
order, so `x-help-card` takes a heading level and that page passes 2.

A third case the browser could not have seen: `/galerie` skips a level
when it is **empty**, because the album headings that carry the `h2` are
the ones that disappear. Caught by the new test, which checks the whole
public site rather than the two pages that were reported.

#### 6 — Two Cache-Control headers

Both `expires` and an explicit `add_header Cache-Control` were set on the
asset locations. nginx's `expires` emits a `Cache-Control` of its own, so
every static file went out with two, which a browser joins into
`max-age=2592000, public, max-age=2592000`. The guess that Laravel was
the second source was wrong — it was nginx twice, in both of the
platform's server blocks. The `expires` directives are gone and the
explicit header stands alone, with a comment so they are not re-added.

#### 7 — Images without width/height

One real: the contact page's decorative icon, a fixed 24 px PNG with no
dimensions, which can shift before the stylesheet arrives. Fixed.

The rest are not. The activity gallery photos are `aspect-square w-full`
— the stylesheet reserves the box, and the intrinsic size of an uploaded
photo is not known, so a `width` attribute would be an invented number.
The admin thumbnails have fixed CSS sizes for the same reason. Measured
CLS of 0 agrees.

### Found while verifying the fixes

Two the browser pass did not report, caught by driving the live site with
the console open.

#### A — An inline script blocked on every visit to `/a-propos/equipe`

| | |
| --- | --- |
| Severity | Mineur, and a Majeur that happened not to be one |

`team.blade.php` pushed a `<script>` block implementing the team
carousel. The policy sent with every page is `script-src 'self'`, so the
browser refused it and logged a violation on every visit.

It did not break the carousel, because `resources/js/app.js` binds the
same `data-carousel-prev` / `data-carousel-next` attributes and the
bundled copy is what has been running all along. So the page carried a
dead duplicate and a console error, and the feature it looked responsible
for worked for another reason entirely — which is why nobody noticed. Had
app.js not had it, the buttons would simply have done nothing.

The block is removed. `PublicPagesTest::test_public_page_ships_no_inline_script`
now walks every public route, so the next inline script fails a test
rather than only a console.

#### B — A replacement character in a screen-reader label

`app.js` held `'Lecteur vid� o'` — the `é` lost to an encoding
accident upstream. It is the fallback `title` of a video iframe, used when
a capsule has no title of its own, so a screen reader would read the
corruption aloud. Fixed.

### Not covered in pass 1

Everything behind a sign-in: parts B through E, and the client-side half
of F. Pass 2 starts there, and starts with the registre separation.

---

## Pass 2 — the authenticated platform, browser only — 29 October 2026

Environment: `https://clinique.40-160-97-168.sslip.io` · commit `dd9c84f`
Tested by: the same browser agent, now driving every screen behind a
sign-in. The project owner typed each password; the agent drove everything
after that.

Coverage: around 97 % of the plan. All nine CMS content types through
create → verify on the public site → delete → verify gone; the
référentiels end to end including the pré-saisine select that reads them;
the complete intake journey with its attachment rules; the registre from
demande through dossier, analyse, the validation gate, assignments,
deadlines, documents and journal; separation in both directions; the
éditeur restrictions; responsive, keyboard and console.

### What held up

The parts of the platform the plan treats as highest-risk are the parts
that passed. Worth recording in full, because it is the first independent
confirmation:

- **Separation holds both ways.** A CMS session opening any registre URL
  gets the *registre* sign-in and no dossier data; a registre session
  opening any admin URL gets the *CMS* sign-in.
- **The registre state machine is enforced server-side.** Posting a
  transition the screen does not offer (`status_id=7`) is refused, not
  merely absent from the dropdown. The supervisor gate opens only once the
  analyse is validated, and a validated analyse cannot be rewritten — the
  lock holds across users, not just across sessions.
- **The journal records twelve distinct action types**, attributed per
  user, and there is no delete anywhere in the registre.
- **Éditeur restrictions return a true 403** on all five protected
  screens, and the links are not even emitted.
- **The intake journey is correct end to end**: all four orientation
  exits, all three attachment rules refused server-side with the client
  `accept` bypassed, nothing written before confirmation, and the
  attachment retained across "Corriger ma demande".
- Rate limits fire on the contact form (the fourth message), the
  newsletter and the sign-in, and the sign-in message does not reveal
  whether an address exists.
- CSV exports carry a UTF-8 BOM with accents intact.

### Findings, and what was done

| | Finding | Severity | Verdict |
| --- | --- | --- | --- |
| 1 | Admin sign-in unreachable below 1024 px | Bloquant | Confirmed · fixed |
| 2 | Nothing in the UI restores or purges a deleted row | Majeur | Confirmed, wrong cause · fixed |
| 3 | Saving an edit from the corbeille lands on the public 404 | Majeur | Confirmed, wrong cause · fixed |
| 4 | Header search does nothing on the dashboard | Majeur | Confirmed, wider · fixed |
| 5 | Notification badge advertises three notifications | Majeur | Confirmed · fixed |
| 6 | A purged attachment returns the raw Symfony page | Mineur | Confirmed, wider · fixed |
| 7 | `/admin/*` 404s render the public page | Mineur | Confirmed · fixed |
| 8 | Edit dialog has no accessible name | Mineur | Confirmed · fixed |
| 9 | "Cette action est irréversible" while a corbeille exists | Mineur | Confirmed · fixed |
| 10 | `validation.in` printed raw | Mineur | Confirmed, far wider · fixed |
| 11 | Interface in English, `<html lang>` still `fr` | Mineur | **Already fixed before the test ran** |
| 12 | Heading order skips H2 | — | **Already fixed** |
| 13 | `/ressources` answers 302 | — | **Already fixed (301)** |
| 14 | Duplicated `Cache-Control` on static assets | — | **Already fixed** |
| 15 | Paginated listings canonicalise to page 1 | — | **Already fixed** |
| 16 | Latency: median 990 ms, 5.70 s peak | — | Network path, not the application |
| 17 | Newsletter may insert duplicate rows | — | Checked: it does not |
| 18 | Étudiant sees every registre screen | — | The awaited permission matrix |

Findings 11–15 were fixed and deployed on 28 October; the crawl that
reported them predates that deploy. Re-measured on 29 October: `lang="en"`
and `og:locale=en_US` on the English interface, no skipped heading level on
any of 22 public pages, `/ressources` 301, exactly one `Cache-Control`
header, listings self-canonicalising with campaign parameters stripped.

#### 1 — The sign-in page could not be used on a telephone

The severity is right and the diagnosis is right. Confirmed in a real
browser at seven viewports: the submit button sits 846 px down a 667 px
screen, `document.scrollHeight === window.innerHeight`, and `scrollY`
stays 0. Unreachable at 375, 500, 768 and 900×1100; reachable from 1024
up. It is the `lg` breakpoint and not the height that decides, which is why
a tall window fails too.

Two things were wrong, not one. The body was pinned with `h-full
overflow-hidden`, so the document could not scroll — and the form panel is
`flex-1` with its own scrollbar, which is correct beside the brand panel
and crushes to nothing beneath it. Both now start at `lg`.

#### 2, 3 — The corbeille, and the 404 it led to

**The routes were never missing.** `/restaurer` and `/definitif` exist for
all nine content types, with the `admin` middleware on the permanent
delete, and the controller actions behind them are complete. What was
missing was any control in `x-admin.row-actions` that reached them. The
suggested fix — "add a restore route and button" — would have duplicated
working code.

Finding 3's mechanism is also different from the report. `update()`
redirects to the listing, correctly; it never redirects to
`/admin/actualites/{id}`. What happens is that `{article:id}` binds the
model, `SoftDeletes` excludes a deleted one, and the request 404s — so the
address bar keeps the URL the form posted to, and it looks like a redirect
to a dead route.

Both are the same defect seen twice: a deleted row was offering the live
row's four controls. It now offers Restaurer and, for an administrator,
Supprimer définitivement — and *Modifier* is gone, so the 404 is
unreachable rather than merely handled.

#### 4, 5 — A search box and a badge that did nothing

The search `<form>` had no `action` at all. On a listing it worked by
accident, because submitting to the current URL hands `q` to a controller
that reads it; on the dashboard — the screen you land on after signing in —
nothing happened. **Wider than reported:** the same is true of paramètres,
référentiels and comptes, so four screens carried a search box that could
not search. The box is now rendered only where a screen declares where its
search goes.

The badge was a hard-coded `3` with `aria-label="3 notifications non
lues"`, linking to the page you were already on, while
`/admin/notifications` did not exist. It also broke the project's own rule
that nothing fictional is rendered as if it were real. It now counts
unhandled contact messages, links to them, and is absent at zero — and
absent for an éditeur, who is refused that screen.

#### 6, 7 — Error pages

Confirmed, and wider than reported: `errors/` contained only 403 and 404.
Every other status fell through to the framework's English page — **419
included, which is where an ordinary visitor lands if their session expires
before they submit a form.** That one matters more than the 410 the
recette found.

The 410 message was never missing: the registre aborts with
`'Cette pièce jointe a été supprimée du serveur de réception.'` and the
default page discarded it. Views now exist for 403, 404, 410, 419, 429,
500 and 503, each preferring the exception's own message, and
`App\Support\ErrorPage` draws anything under `/admin` or `/registre` in a
plain shell instead of the public layout. The designed public 404 is
unchanged.

#### 10 — The validation key was the symptom, not the defect

`lang/fr/validation.php` did not exist, so **every** validation key
resolved to itself — `validation.required`, `validation.email`,
`validation.mimes`, all of them. The screens looked French because nearly
every FormRequest writes its own messages by hand; `CallRequest` writes
none, so every one of its refusals printed a key. Adding the `in` key, as
suggested, would have fixed one of about a hundred.

The complete French set is now in place, with an `attributes` map for the
field names several screens share, and a test that walks every rule the
application uses.

#### 16, 17, 18 — Not defects

The latency figures are real and were measured from Karachi; the reporter
recorded them as network path rather than application defect, which is
correct, and is what the loopback figures say too. The newsletter was
checked on the live database: one row per address, no duplicates, so the
idempotent success message is idempotent underneath as well. The étudiant
profile seeing every registre screen is the permission matrix the
specification lists as awaiting the Clinic's approval.

#### The open question, answered

Signing into the registre did not end the CMS session — `store()` calls
`session()->regenerate()`, which keeps the other guard's login. Signing
*out* does: the two guards share one session cookie and
`session()->invalidate()` is guard-blind, so leaving the registre leaves
the CMS too. That is the safer behaviour for a confidential register on a
shared faculty machine and is kept deliberately; it is now written down in
`CONTEXT.md` so it is not read as a bug a second time.

#### Reported as tester error, and correctly

Three claims were withdrawn by the reporter before they reached this
document: a dialog that appeared to close on empty submit (an artefact of
scripted clicking), oversized images that are `<picture>` fallbacks never
served, and three content types that appeared absent from the public site
(two hidden by `is_visible`, one on page 2). They are recorded because
withdrawing a claim after checking it is the behaviour that makes the rest
of a report worth reading.

### Remarques — the three that were acted on

The recette's *"works, but reads badly"* list held eight observations.
None was a defect; each left someone guessing. Three were worth closing
and are done, one turned out to be a measurement artefact, and the rest
are recorded below as decisions rather than work.

**The corbeille had no way in.** The counter above each listing said how
many rows were in it without linking to them, so the only route was to
type `?statut=corbeille`. Harmless while nothing could be restored, and
not harmless now. The card is a link on the seven listings that count it;
`activites` and `fiches-pratiques` keep their four slots for photos,
téléchargements and domaines, and reach the corbeille through the statut
filter that every listing carries.

**A dialog opened on the way out of itself.** `showModal()` focuses
whatever comes first in the markup, which is the close button. The caret
now lands on the first real field.

**The visibility toggle forgot a choice without saying so.** An editor who
turned it off and hit a validation error got it back on. `old()` cannot
express this alone — an absent checkbox returns the default and reads as
checked — so the presence of old input is the signal.

#### One that was not a defect

**"Two overlapping visibility concepts"** — a row that is `status =
Publié` and still hidden because `is_visible` defaults off. Checked in the
code: the create form defaults the toggle to **on**, and the controller
reads it with `$request->boolean()`, so a row created through the
interface is visible. The agent created those rows by posting directly,
without the field, which is why they came out hidden. Nothing changed.

Worth knowing for whoever touches it: the toggle is a bare checkbox with
no hidden companion, so an unchecked one posts nothing. It is the
`boolean()` call that makes unchecking mean *false* rather than *leave it
as it was*. Remove it and hiding a partner from the edit dialog silently
stops working — `AdminAffordancesTest` asserts it does not.

#### Left as they are, deliberately

| | Why |
| --- | --- |
| Consent box clears on "Corriger ma demande" | Re-consent after editing a request is defensible. The Clinic should decide, not the code. |
| Native HTML5 validation bubbles follow the browser's language | The browser draws them. Replacing them means replacing every `required` with scripted validation, against a page that ships no inline script. |
| Two pages share the title "Session de sensibilisation des jeunes" | Seeded demonstration content, not a defect. |
| Mobile filter strips scroll horizontally with no visual hint | A real affordance gap, but the fix is a design decision about fades or arrows. |
| `/pre-saisine/confirmation` keeps rendering on reload | Session-scoped, so nothing leaks. Whether it should expire is the Clinic's call. |

### Still open

- **The two safeguards deliberately not attempted** — disabling your own
  account, and demoting the last administrator. Both are expected to be
  refused, and both are asserted in `UserManagementTest`, but neither was
  tried against the live server because `admin@clinique-fsjp.ne` is the
  only administrator there. To close them, create a second administrator
  first.
- **Left behind by the run:** two QA éditeur accounts, and thirteen rows
  across the corbeilles. Both can now be cleared from the interface —
  which is itself the proof that findings 2 and 3 are fixed.
- Brotli is not enabled in nginx: around 15–20 % off every text asset, no
  code change. Left with the rest of the hosting to UAM IT.
