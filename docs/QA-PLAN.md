# QA plan — Clinique Juridique FSJP-UAM

A full pass over the platform: every public page, every administration
screen, every registre screen, every role, and every create / read /
update / delete path, with the public side verified after each change.

This file is the plan. The run produces `docs/QA-FINDINGS.md`, which is
the report.

Scope at the time of writing: **35 public routes · 20 administration
screens · 13 registre screens · 95 write routes · 2 account systems · 6
roles**.

---

## 0. Before starting

```bash
git pull --ff-only
composer install && npm install
npm run build                 # the compiled CSS is gitignored; without
                              # this you are testing yesterday's styles
php artisan migrate --seed
php artisan migrate --database=registre --path=database/migrations/registre
php artisan db:seed --class=RegistreSeeder
php artisan db:seed --class=RegistreDossierSeeder
php artisan serve
php vendor/bin/phpunit         # must be green before manual testing
```

Record the commit under test: `git log --oneline -1`.

### Accounts

| System | Account | Role |
| --- | --- | --- |
| CMS `/admin/login` | `adminclinique@gmail.com` / `password` | Administrateur |
| CMS | `editeurclinique@gmail.com` / `password` | Éditeur |
| Registre `/registre/connexion` | `coordination@registre.test` | Coordinateur |
| Registre | `encadrant@registre.test` | Encadrant |
| Registre | `etudiant@registre.test` | Étudiant |
| Registre | `technique@registre.test` | Admin technique |
| Registre | `compte-desactive@registre.test` | **disabled — must fail** |

All registre passwords: `registre-demo-2026`.

---

## 1. Public pages — every one of them

Open each. For every page record: HTTP status, console errors, failed
requests, broken images, and every link followed one level deep.

```
/                                    /services
/a-propos                            /services/processus-de-consultation
/a-propos/equipe                     /services/conditions-acces-gratuite
/partenaires                         /services/tarifs-administratifs
/actualites                          /ressources/fiches-pratiques
/actualites/{slug}                   /ressources/fiches-pratiques/{slug}
/activites                           /ressources/rapports-annuels
/activites/{slug}                    /web-tv
/galerie                             /web-tv/{slug}
/rejoindre                           /contact
/recherche                           /pre-saisine
/mentions-legales                    /conditions-utilisation
/politique-confidentialite           /une-adresse-inexistante  (404 page)
```

**On every page check:**

- no 404 on any asset (images, CSS, JS, fonts) — the network panel is the
  only honest witness here;
- every `<img>` renders; none shows its alt text instead;
- every link reaches a real page, and a detail link reaches **its own**
  item rather than a listing;
- no raw PHP, no `{{`, no `Undefined variable` anywhere in the output;
- no horizontal scrollbar at 375 px.

**Per-page specifics:**

| Page | Check |
| --- | --- |
| `/` | News, Web TV and activity blocks show real seeded rows and each card links to its own item; partner strip shows Niger partners |
| `/actualites` | Rubrique pills filter; search finds a known word; archives by year; sidebar counts match the list |
| `/actualites/{slug}` | Open two different articles — each shows **its own** title, image and body |
| `/activites`, `/galerie` | Photos load; gallery counts match |
| `/ressources/*` | PDF download works and the counter rises |
| `/web-tv/{slug}` | Player loads **only after a click** |
| `/recherche` | Search across types; empty search; a one-character term |
| `/404` | Styled page, not a stack trace |

---

## 2. Language — FR / EN

1. Switch to EN on `/actualites`. You must stay on `/actualites`.
2. Navigation, buttons, breadcrumbs, panel titles and dates are English.
3. Switch back to FR. Everything returns.
4. Navigate three pages while in EN — the choice persists.
5. `/langue/de` → 404.

**Not a bug:** page body text stays French. Only the interface is
translated.

---

## 3. Public forms

### Contact `/contact`

| Case | Expected |
| --- | --- |
| Empty submit | Field errors, nothing saved |
| Invalid e-mail | Error |
| Valid submit | Confirmation message |
| 4 submissions in a row | Fourth refused (rate limit) |
| Then `/admin/messages` | The message is there, with its content intact |

### Newsletter (sidebar of `/actualites`)

Subscribe; subscribe again with the same address; check
`/admin/newsletter`; unsubscribe via the token link.

### Pré-saisine `/pre-saisine` — the longest path, test it hardest

**Each of the four exits, from a fresh start:**

| Answer | Expected |
| --- | --- |
| Urgence = Oui | Emergency page with the telephone, journey ends |
| Avocat = Oui | ANAJJ referral |
| Domaine = Non | "sort de nos domaines" |
| Déplacement = Non | Niamey-only explanation |

**Then the full journey:**

1. Submit the form empty → errors on nom, téléphone, domaine, objet, consentement.
2. Attach a `.exe` → refused. Attach 6 files → refused. Attach a >5 Mo file → refused.
3. Attach a valid PDF, tick consent, submit.
4. **Review page** — everything you typed is shown; nothing is saved yet.
5. "Corriger ma demande" → back to the form with your data intact.
6. Submit → reference `PS-YYYY-NNNN` displayed.
7. In a new tab, go straight to `/pre-saisine/formulaire` → redirected to the orientation.
8. Reload `/pre-saisine/confirmation` after finishing → should not show someone else's reference.

---

## 4. Administration — every screen, every operation

Sign in as **administrateur**.

### 4.1 The CRUD routine — run it on all nine content types

Actualités · Web TV · Fiches pratiques · Rapports annuels · Pages ·
Appels à candidatures · Activités & caravanes · Équipe · Partenaires

For each:

| Step | Check |
| --- | --- |
| Open "Ajouter" | Dialog opens centred, scrolls, **no blank band, no overflow**, closes on Escape and on backdrop |
| Submit empty | Field errors, dialog stays open |
| Create with every field + image/PDF | Saved; the uploaded file is reachable |
| **Verify on the public site** | The item appears, with its image, in the right section |
| Edit, change several fields | Public page reflects every change |
| Duplicate | Copy created, original untouched |
| Delete | Moves to corbeille; **gone from the public site** |
| Restore | Back in the list and back on the site |
| Permanent delete (admin only) | Gone for good |

**Publication states, on at least three types:**

- Brouillon → not on the public site, even by direct URL
- Programmé (tomorrow's date) → **not visible**
- Published, yesterday's date → visible
- Archivé → not visible

**Also check:** sorting, pagination beyond one page, search and filters on
each listing, and the counters at the top matching reality.

### 4.2 Référentiels `/admin/referentiels`

Add, edit and delete a fee, a legal domain and a category. Each change
must show on the public pages that read them (`/services/tarifs-administratifs`,
`/services`, the rubrique filters). Delete one that is in use and record
what happens.

### 4.3 Paramètres `/admin/parametres`

Change phone, e-mail, address, hours → verify in the header, the footer
and `/contact`. Enable the bandeau → visible site-wide → disable → gone.
Upload a logo.

### 4.4 Messages and newsletter

Mark a message handled, delete one. Export the newsletter to CSV and open
it in a spreadsheet — accents must be correct.

### 4.5 Utilisateurs `/admin/utilisateurs`

| Step | Expected |
| --- | --- |
| Create an éditeur (password < 12 chars) | Refused |
| Create with a duplicate address | Refused |
| Create properly, then sign in as them | Works |
| As éditeur: sidebar | **No "Administration" group** |
| As éditeur: type `/admin/parametres` | **Accès refusé** |
| As éditeur: `/admin/utilisateurs` | Accès refusé |
| As éditeur: create an actualité | Works |
| As admin: disable the éditeur, try their login | Fails |
| Disable your own account | Refused |
| Demote the last administrator | Refused |
| Reset someone's password, sign in with it | Works |

### 4.6 Mon compte `/admin/mon-compte`

Change your name. Change your password with the wrong current password →
refused. With the right one → works, and the old password no longer signs
you in.

---

## 5. Registre numérique — every screen

### 5.1 Separation — do this first

| Test | Expected |
| --- | --- |
| Signed into the CMS, open `/registre` | Registre sign-in, **not** the CMS one |
| Signed into the registre, open `/admin` | CMS sign-in |
| `compte-desactive@registre.test` | Sign-in fails |
| Sign in with 5 wrong passwords | Locked for a few minutes |

### 5.2 Demandes en ligne

Use the pré-saisine you submitted in §3.

1. It appears in the list with the right applicant, domain and attachment count.
2. Open it — everything the visitor typed is shown.
3. Download the attachment — it opens.
4. **Accepter** → a dossier is created, its reference shown.
5. Download the same attachment again → **410, "supprimée du serveur de réception"**. That is correct.
6. On another submission: **Refuser** with a reason → no dossier created, attachments gone.
7. Try accepting an already-accepted one → refused.

### 5.3 Dossiers

- Search by reference, by surname, by telephone, by subject.
- Filter by statut, by urgence, by **en retard**.
- **Exporter CSV** → opens in a spreadsheet, accents intact, contains exactly the filtered rows.
- **Nouveau dossier** → create one for a walk-in; submit empty first (errors); try a future date (refused).

### 5.4 One dossier — the whole fiche

| Section | Test |
| --- | --- |
| Statut | Only declared moves offered; make one; history gains a line with your name and the time; try a terminal status → dossier closes |
| Affectations | Add a student and a supervisor; add the same person twice → refused; end an assignment → leaves the active list, stays in the record |
| Échéances | Add one in the past → red, appears under "en retard"; mark it done → no longer late |
| Documents | Upload a mandat signé; download it; try a `.exe` → refused |
| Analyse | Write and submit → "en attente de validation"; **try the status that requires validation → refused**; validate → that status opens; try rewriting a validated analysis → refused |

### 5.5 Journal `/registre/journal`

Every action above must be here, with who and when: `dossier.viewed`,
`document.downloaded`, `status.changed`, `presaisine.accepted`,
`assignment.created`, `dossier.exported`. Filter by action and by
account.

### 5.6 Comptes `/registre/comptes`

Create an account, sign in as it, disable it, try signing in again.
Disable your own → refused. Change your own password from
`/registre/mon-compte`.

---

## 6. Security

| Test | Expected |
| --- | --- |
| Signed out, open any `/admin/*` | CMS sign-in |
| Signed out, open any `/registre/*` | Registre sign-in |
| Guess a document URL under `storage/` | Not reachable |
| Download another dossier's document through your dossier's URL | 404 |
| View source on an admin page | `noindex, nofollow` present |
| View source anywhere | No raw PHP, no stack trace, no database error |
| Leave a session 30 minutes | Signed out |

---

## 7. Cross-cutting

- **Responsive**: 375 px, 768 px, 1440 px on the home page, a listing, a
  detail page, an admin listing and a dossier fiche.
- **Browsers**: Chrome and Firefox at minimum; note anything that differs.
- **Without JavaScript**: the public site must still read and navigate.
- **Keyboard**: tab through the main navigation and one form; focus must
  always be visible.
- **Weight**: home page under 500 Ko, inner pages under 400 Ko.

---

## 8. Known — do not report these

| | Why |
| --- | --- |
| Browser says "Not secure" | No domain yet; HTTPS is UAM's |
| `Statut provisoire 01…13` | The Clinic has not supplied the real labels |
| All four registre profiles see the same thing | The permission matrix is awaited |
| No e-mail arrives | SMTP credentials are awaited |
| Body text stays French in EN | Only the interface is translated |
| Figures like "500+" | Examples, to be confirmed by the Clinic |
| Content is fictitious | Required during development |

---

## 9. What the report must contain

Write `docs/QA-FINDINGS.md`:

- the commit tested, the date, the environment and the browsers used;
- a one-paragraph summary: what works, what is broken, what is doubtful;
- a table of findings, **worst first**, each with: where, what was done,
  what happened, what should have happened, severity, and a screenshot;
- a section listing every page opened with its result, so that what was
  **not** covered is visible;
- broken links, missing images and console errors, each with its page;
- anything that works but reads badly — wording, layout, confusing labels
  — kept separate from the defects.

**Severity:**

| | |
| --- | --- |
| **Bloquant** | Data lost, a page down, someone sees what they must not |
| **Majeur** | A feature does not work; a workaround exists |
| **Mineur** | Visual, wording, an edge case |
| **Remarque** | Works, but would be better otherwise |

Report what happened, not what should be concluded from it. A finding
nobody can reproduce from the report is not yet a finding.
