# Context for whoever works on this next

The README says what the project is and how to run it. The two other
documents in this folder are written for the Clinic and for UAM IT. This
one is for the next person in the code: the decisions that are not
visible from the files, what is deliberately missing, and the things that
cost a day to find out.

Last updated: 29 October 2026 · 643 tests

---

## 1. Where it stands

| Module | State |
| --- | --- |
| 1 — Architecture, foundations, shared UI | Done |
| 2 — Public site, Web TV, CMS | Done, live |
| 3 — Pré-saisine and Registre | Done, live, running on provisional statuses |
| 4 — Security, QA, documentation, deployment | Mostly done; infrastructure is UAM's |

Everything is deployed at `http://40.160.97.168:8080`. Local and live are
normally on the same commit — check before assuming.

---

## 2. Decisions that are easy to undo by accident

### Two databases, and nothing crosses between them

The plan requires that confidential case data never sit in the public
site's database. So:

- the `registre` connection is a **separate database** with a **separate
  MySQL account** whose rights stop at it;
- every model under `App\Models\Registre` uses `OnRegistreConnection`.
  A model added there without the trait would quietly write to the public
  database — that is the one mistake this layout invites;
- **no foreign key crosses the two.** A dossier's legal domain is copied
  as text rather than joined, and that is also right: a dossier keeps the
  wording it was opened under;
- pré-saisine submissions are written to the registre database even
  though the public site receives them. A name, a telephone number and
  the account of someone's problem are exactly what the rule is about.

`DossierSchemaTest` and `PresaisineTest` both assert the separation. If
one of them starts failing, something crossed the line.

### Nothing names a dossier status

The Clinic has not approved the thirteen labels, and the plan says in so
many words not to invent them. So statuses are **rows**, transitions are
**rows**, and the supervisor gate is a **flag on a row**
(`requires_supervisor_validation`). No status is named anywhere in the
code — not in a constant, not in a condition.

What is seeded today is numbered (`Statut provisoire 01…13`) with a
provisional path between them, so the workflow can be demonstrated. When
the Clinic sends its list, `RegistreDossierSeeder::statuses()` is
rewritten and **nothing else changes**. Keep it that way.

### An account is disabled, never deleted

Both in the CMS and in the registre. Deleting a CMS account would blank
`author_id` on everything it published; deleting a registre account would
strand assignments and audit entries. Four rules stop the Clinic locking
itself out — see `UserManagementTest`, which is the readable statement of
them.

### The audit log outlives its subject

`audit_logs` keeps `subject_type` + `subject_id` rather than a foreign
key. A log that disappears with what it describes erases itself exactly
where it matters.

### Emails never repeat what someone wrote

A mail travels unencrypted into an inbox the Clinic does not control. The
acknowledgement carries the reference; the Clinic's notice carries the
reference, the domain and a count. The detail stays behind a sign-in.
`PresaisineNotificationTest` asserts this.

### A listing page's canonical keeps its parameters

`url()->current()` drops the query string. Used for a canonical URL, it
tells a search engine that page two of a listing *is* page one, and that
every rubrique filter is the unfiltered page — an instruction to drop all
of them. `App\Support\Seo::canonical()` keeps the parameters that change
which items a page shows, drops the ones that only record where a visitor
came from (`utm_*`, `fbclid`, `ref`), and sorts what remains so that two
orderings of the same filters are one address rather than two.

**There are deliberately no hreflang alternates.** English is an interface
translation at the same URL, chosen by cookie, and page body text stays
French. Alternates would advertise two addresses that are one address,
holding near-identical pages. They become correct the day the content is
translated and each language has its own URL. The reasoning is in the
class, so it is not re-litigated from the symptom.

### Heading levels are part of the design, not of the markup

Card components are `h3`, because everywhere they appear in the design a
section heading sits above them. On a listing with no such heading the
page goes from `h1` to `h3`, which a screen reader reports as a skipped
level. Two pages needed a visually hidden `h2`; `x-help-card` takes a
`level` prop for the one page where it comes first in document order.

`SeoMetadataTest::test_no_public_page_skips_a_heading_level` walks every
public page rather than the ones that were reported — which is how the
empty-gallery case was found, since the headings that disappear when
there are no albums are the `h2`s.

### An error page is drawn in the shell of the area it happened in

`resources/views/errors/` held only 403 and 404, and both extended the
public layout. So a 404 on an admin URL answered with the public site's
header, the Clinic's telephone number and the FR/EN switcher — the
administrator was ejected from the area they were working in — and every
other status fell through to the framework's own English page. The
registre raises `abort(410, 'Cette pièce jointe a été supprimée du serveur
de réception.')`, and that sentence was being thrown away in favour of
*"Something is broken. We will fix it as soon as possible."*

`App\Support\ErrorPage::layout()` picks `layouts.error` — a plain,
auth-independent shell — for anything under `/admin` or `/registre`, and
`layouts.app` elsewhere, so the designed public 404 is untouched. Every
status the application can raise now has a French view (403, 404, 410,
419, 429, 500, 503) and each one prefers the exception's own message over
its default. `ErrorPagesTest` asserts all of it, including that the public
404 keeps its public chrome.

**419 is the one to keep in mind.** A form submitted after the session has
expired lands there, which makes it the error an ordinary visitor is most
likely to see.

### A deleted row offers restore, not edit

The `restaurer` and `definitif` routes existed for all nine content types
from the start — but `x-admin.row-actions` never offered them, so the
corbeille stored items nobody could get back. It did offer *Modifier*,
which was worse: the update route binds the model, `SoftDeletes` excludes a
deleted one, and saving that form was answered with a 404.

A row in the corbeille now carries exactly two controls, Restaurer and
(for an administrator) Supprimer définitivement. The component decides
from `restoreAction` / `forceDeleteAction` being set, and the view decides
from `$row->trashed()`.

**The delete confirmation is the form's to write.** Moving a row to the
corbeille is reversible and a permanent delete is not, so each form
carries its own `data-confirm` rather than `app.js` guessing from the
button label. "Cette action est irréversible" was true only while nothing
could be restored.

### A control that cannot work is not rendered

The topbar's search `<form>` had no `action`, so it submitted to whatever
page you were on — and on the four screens whose controller never reads
`q` (the dashboard you land on after signing in, paramètres, référentiels,
comptes) the page simply re-rendered. The badge beside it was worse: a
hard-coded `3`, an `aria-label` reading "3 notifications non lues", and a
link to the page you were already on, while `/admin/notifications` was a
404.

The search box is now rendered only on a screen that declares
`@section('search_action', …)`, and the badge counts unhandled contact
messages, links to them, and disappears at zero. Both follow the rule that
was already written in `row-actions`: *null hides the button entirely
rather than showing a control that does nothing.*

### Validation messages come from a file, not from each request

There was no `lang/fr/validation.php`, so every validation key resolved to
itself. The forms looked French only because nearly every FormRequest
writes its own `messages()` by hand — and any rule those files had not
anticipated printed its key to the Clinic. `CallRequest` has no
`messages()` at all. The recette found one symptom, `validation.in` on the
équipe screen; the cause was the whole file missing.

`lang/fr/validation.php` now carries the complete set plus an `attributes`
map for the field names several screens share. Per-field wording stays in
the request that owns it. `ValidationMessagesTest` walks every rule used
anywhere in the application, so a new rule cannot arrive without a
sentence behind it.

### English is served from this repository

Google Translate was tried and removed: its widget rewrites the page with
inline scripts, which means loosening the Content-Security-Policy, and
translating a page sends its text to Google — including, on the
pré-saisine, someone's account of their own situation. English now comes
from `lang/en.json`, French is the fallback, and untranslated strings are
served in French rather than as missing keys. That is what lets pages be
translated one at a time.

---

## 3. Conventions

- **Comments say why, not what.** The ones worth keeping explain a
  decision someone would otherwise undo.
- **Commit messages are in French**, prose, explaining the reasoning.
- **Tests are named as sentences** and assert behaviour, not
  implementation. A test that needs the registre schema declares it with
  `RefreshesRegistreDatabase`.
- **Blade components over repetition** — `x-admin.*` is the design
  system; the registre uses the same components, deliberately.
- **Nothing fictional is rendered as if it were real.** Where the schema
  holds nothing, the screen says so rather than showing an invented
  figure. Several mock-up sections were dropped for this reason.

---

## 4. Things that cost a day to find out

**A two-panel layout that pins itself to the viewport height traps its
own form.** The admin sign-in had `h-full overflow-hidden` on the body and
`flex h-full flex-col lg:flex-row` on the wrapper. Below `lg` the panels
stack, so the form sat beneath a full-height brand panel — and the body
could not scroll to it. Unreachable on every viewport under 1024 px,
including 900x1100, because the breakpoint and not the height decides the
stacking. The viewport-height pinning now starts at `lg`, where the panels
are actually side by side.

**Signing out of one area signs you out of both.** The `web` and
`registre` guards are separate, but they share one session cookie, and
`session()->invalidate()` on logout is guard-blind. Signing in is fine —
`regenerate()` keeps the other guard's login. This is deliberate for a
confidential register on a shared faculty machine, and it is the answer to
"why did my CMS session end when I left the registre".

**OPcache keeps compiled Blade in shared memory.** Two deploys went out
without `systemctl reload php8.5-fpm` and the site kept serving the old
templates while every file on disk was correct. It is the last line of
`scripts/deploy.sh` and it is not optional.

**`public/build` is gitignored.** `git pull` never updates the compiled
CSS. A Tailwind class added in a Blade file does not exist until
`npm run build` runs. A "fix that did not work" is this, most of the
time.

**A closed `<dialog>` with a `grid` class is not hidden.** The utility
beats the browser's `display: none`, and every dialog on the screen gets
laid out below the content. `x-admin.edit-modal` uses `hidden open:grid`.

**Cookies are encrypted.** A cookie written by JavaScript arrives in the
clear and fails to decrypt — it reads as absent. The language cookie is
set server-side for this reason, and `SetLocale` is appended to the web
group, not prepended, so it runs after cookies are decrypted.

**SQLite and MySQL disagree.** MySQL accepted `HAVING` on a query with no
`GROUP BY`; SQLite refused it, and fifteen tests failed while the live
site looked fine. Driver-dependent SQL needs branching —
`Article::archiveYears()` is the pattern.

**The server has no `pdo_sqlite`.** The registre had to be MySQL there.

**Blade escapes apostrophes.** `assertSee('…d\'accès…', escape: false)`
fails. Assert on a fragment without one.

**PHPUnit reserves `status()`.** A test helper by that name is a fatal
error, not a failure.

**nginx's `expires` emits its own `Cache-Control`.** Setting both
`expires 30d;` and `add_header Cache-Control "public, max-age=2592000"`
sends the header twice, and the browser joins them into
`max-age=2592000, public, max-age=2592000`. Keep the `add_header`, which
can say `immutable`, and leave `expires` out.

**A reload leaves the old workers serving.** For a few seconds after
`systemctl reload nginx`, a request can still be answered by a worker
holding the previous configuration — so the check that runs in the same
command as the reload reports the old behaviour and the fix looks like it
did nothing. Verify on a fresh connection, a moment later.

---

## 5. What is deliberately missing

| | Why |
| --- | --- |
| Permission matrix enforcement | The Clinic has not approved it. Every registre account currently sees everything, and every action is logged. |
| Real status labels | Same reason; the mechanism is in place. |
| Dossier retention (5 years / 1 year) | Both count from a closure the thirteen statuses define. |
| Backups, the registre's own subdomain | UAM IT's. |
| Rich-text editor | Not asked for by the plan; a library costs 45–200 Ko against a performance budget. |
| Width variants for uploaded images | Bundled artwork has them; uploads do not. |

---

## 6. Working on the server

```bash
ssh hxpeed@40.160.97.168                    # key: ~/.ssh/clinique_deploy
cd /var/www/clinique && sudo bash scripts/deploy.sh
```

Two addresses answer: `https://clinique.40-160-97-168.sslip.io` (a real
certificate, added later) and the original `http://40.160.97.168:8080`.
The nginx file for the first is `clinique-sslip`, deliberately separate
from the other two sites' files.

`sanem` shares the host and the php-fpm pool. Nothing in a deploy should
touch it — the nginx server block, the databases and the application
directory are all separate, and it stays that way.

**Still to do on that server:** the scheduler cron line (see the technical
documentation). Without it the retention rules never run.
