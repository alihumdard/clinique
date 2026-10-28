# Context for whoever works on this next

The README says what the project is and how to run it. The two other
documents in this folder are written for the Clinic and for UAM IT. This
one is for the next person in the code: the decisions that are not
visible from the files, what is deliberately missing, and the things that
cost a day to find out.

Last updated: 28 October 2026 · `82bda8d` · 522 tests

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

---

## 5. What is deliberately missing

| | Why |
| --- | --- |
| Permission matrix enforcement | The Clinic has not approved it. Every registre account currently sees everything, and every action is logged. |
| Real status labels | Same reason; the mechanism is in place. |
| Dossier retention (5 years / 1 year) | Both count from a closure the thirteen statuses define. |
| HTTPS, backups, registre subdomain | UAM IT's. |
| Rich-text editor | Not asked for by the plan; a library costs 45–200 Ko against a performance budget. |
| Width variants for uploaded images | Bundled artwork has them; uploads do not. |

---

## 6. Working on the server

```bash
ssh hxpeed@40.160.97.168                    # key: ~/.ssh/clinique_deploy
cd /var/www/clinique && sudo bash scripts/deploy.sh
```

`sanem` shares the host and the php-fpm pool. Nothing in a deploy should
touch it — the nginx server block, the databases and the application
directory are all separate, and it stays that way.

**Still to do on that server:** the scheduler cron line (see the technical
documentation). Without it the retention rules never run.
