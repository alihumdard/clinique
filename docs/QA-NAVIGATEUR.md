# Browser QA — testing the live platform without the code

For an agent working **inside a browser only**: Claude in Chrome, or a
person. No repository, no terminal, no database. Everything here is done
by opening a URL, clicking, typing and looking.

The companion plan, `QA-PLAN.md`, is for someone who has the code. This
one is narrower on purpose: it only asks for what a browser can actually
see.

**Target:** http://40.160.97.168:8080

---

## 0. What this agent can and cannot establish

It can see: what renders, what a link does, what a form accepts and
refuses, what the network panel reports, what the console prints, how a
page behaves at phone width, and whether a change made in an
administration screen appears on the public site.

It cannot see: the database, the logs, the mail queue, or the code. So
"the record was saved" is never observed directly — it is inferred from
the public page, which is the right way round anyway, because that is
what the Clinic will see.

When something cannot be established from the browser, the report says
so. A guess written as a finding wastes more time than a gap.

---

## 1. Rules for working on a live site

The client is testing this same address. Everything created here is
visible to them.

1. **Prefix everything you create with `QA-`.** A news item is
   `QA-TEST actualité 1`, a dossier subject is `QA-TEST licenciement`, an
   account is `qa-test-editeur@example.test`.
2. **Delete what you created**, at the end of the run. Note anything you
   could not delete.
3. **Do not touch rows you did not create.** Do not delete the seeded
   dossiers, the seeded articles, or the existing accounts.
4. **Never permanently delete** (the "définitif" action) anything that
   was already there.
5. **Do not change the site settings** (telephone, e-mail, logo) without
   putting them back. Note the original values first.
6. **Never disable the only administrator**, and do not change the
   password of an account you were given.

---

## 2. Accounts

| Where | Account | Password | Role |
| --- | --- | --- | --- |
| `/admin/login` | `admin@clinique-fsjp.ne` | *(supplied separately)* | Administrateur |
| `/admin/login` | *you create one* | — | Éditeur |
| `/registre/connexion` | `coordination@registre.test` | `registre-demo-2026` | Coordinateur |
| `/registre/connexion` | `encadrant@registre.test` | `registre-demo-2026` | Encadrant |
| `/registre/connexion` | `etudiant@registre.test` | `registre-demo-2026` | Étudiant clinicien |
| `/registre/connexion` | `technique@registre.test` | `registre-demo-2026` | Admin technique |
| `/registre/connexion` | `compte-desactive@registre.test` | `registre-demo-2026` | **Disabled — must fail** |

The four registre profiles currently see the same thing. That is awaited
work, not a defect.

---

## 3. Public pages

Open every one. For each, record: did it load, console errors, failed
network requests, broken images, and whether every link went somewhere
real.

```
/                      /services                             /web-tv
/a-propos              /services/processus-de-consultation   /web-tv/{une capsule}
/a-propos/equipe       /services/conditions-acces-gratuite   /rejoindre
/partenaires           /services/tarifs-administratifs       /contact
/actualites            /ressources/fiches-pratiques          /recherche
/actualites/{un article}  /ressources/fiches-pratiques/{une fiche}
/activites             /ressources/rapports-annuels          /mentions-legales
/activites/{une activité} /galerie                           /conditions-utilisation
/pre-saisine           /politique-confidentialite            /une-adresse-inexistante
```

On each: no asset 404 in the network panel, every image renders, no
horizontal scroll at 375 px, no raw `{{`, no "Undefined variable", no
stack trace.

**Open two different articles** and two different capsules. Each must
show its own content — this is where a page that serves the same item to
every link shows itself.

**Follow every link once**, including the footer. A link that returns to
the page it was on is a finding.

---

## 4. Language

On `/actualites`, switch to **EN**. You must stay on `/actualites`, and
the navigation, buttons, breadcrumb and dates become English. Navigate
three pages — the choice holds. Switch back to **FR**.

The body text of pages stays French. That is intended.

---

## 5. Public forms

### Contact

Empty submit → errors. Bad e-mail → error. Valid → confirmation. Four in
a row → the fourth refused.

### Newsletter

Subscribe; subscribe again with the same address.

### Pré-saisine — the longest journey, test it hardest

**Each exit separately, starting fresh each time at `/pre-saisine`:**

| Answer | Expected |
| --- | --- |
| Urgence = **Oui** | Emergency page with the telephone; journey ends |
| Avocat = **Oui** | ANAJJ referral |
| Domaine = **Non** | "sort de nos domaines" |
| Déplacement = **Non** | Niamey-only explanation |

**Then the whole path:**

1. Submit the form empty → errors on nom, téléphone, domaine, objet, consent.
2. Try attaching a `.exe` → refused. Six files → refused. A file over 5 Mo → refused.
3. Fill it in (subject starting `QA-TEST`), attach a PDF, tick consent.
4. On the review page: everything you typed is shown, and nothing is saved yet.
5. "Corriger ma demande" → the form returns with your data.
6. Send → a reference `PS-YYYY-NNNN` appears. **Write it down**; you will
   find it again in the registre.
7. In a new tab, open `/pre-saisine/formulaire` directly → it must send
   you back to the orientation.

---

## 6. Administration

Sign in as administrateur.

### 6.1 The routine, on each content type

Actualités · Web TV · Fiches pratiques · Rapports annuels · Pages ·
Appels à candidatures · Activités & caravanes · Équipe · Partenaires

| Step | What to look at |
| --- | --- |
| "Ajouter" | Dialog centred, scrolls, **no blank band under the buttons, no overflow**; closes on Escape and on a backdrop click |
| Submit empty | Errors shown, dialog stays open |
| Create (`QA-TEST …`) with an image | Appears in the list |
| **Go to the public site** | It is there, with its image, in the right place |
| Edit several fields | The public page shows every change |
| Duplicate | A copy appears; the original is untouched |
| Delete | In the corbeille, and **gone from the public site** |
| Restore | Back in both |

**Publication states**, on at least three types: brouillon → invisible
publicly even by direct URL; programmé for tomorrow → **invisible**;
published yesterday → visible.

Also: the filters and the search on each listing, pagination past page
one, and whether the counters at the top match what the list shows.

### 6.2 Référentiels, paramètres, messages, newsletter

- A fee, a domain and a category: add, edit, delete → check the public
  pages that read them.
- Paramètres: **note the current values**, change the telephone → check
  header, footer and `/contact` → **put it back**. Enable the bandeau →
  visible site-wide → disable it again.
- `/admin/messages`: the contact message you sent in §5 is there, intact.
- `/admin/newsletter`: export the CSV, open it, check the accents.

### 6.3 Utilisateurs — the role test

| Step | Expected |
| --- | --- |
| Create an éditeur, password under 12 characters | Refused |
| Same address as an existing account | Refused |
| Create `qa-test-editeur@example.test` properly | Created |
| Sign in as them: sidebar | **No "Administration" group** |
| Type `/admin/parametres` in the address bar | **Accès refusé** |
| Type `/admin/utilisateurs` | Accès refusé |
| Create an actualité as the éditeur | Works |
| Back as admin: disable them, try their sign-in | Fails |
| Try to disable your own account | Refused |
| Try to demote the last administrator | Refused |

### 6.4 Mon compte

Change your display name. Try a password change with the wrong current
password → refused. (Do **not** change the password of the account you
were lent.)

---

## 7. Registre

### 7.1 Separation — do this first

| Test | Expected |
| --- | --- |
| Signed into the CMS, open `/registre` | **Registre** sign-in, not the CMS one |
| Signed into the registre, open `/admin` | CMS sign-in |
| Sign in with `compte-desactive@registre.test` | Fails |
| Five wrong passwords | Temporarily locked |

### 7.2 The pré-saisine you sent

1. Find your `PS-…` reference under **Demandes en ligne**.
2. Open it — everything the visitor typed is shown.
3. Download the attachment.
4. **Accepter** → a dossier `CJ-…` is created.
5. Download the same attachment again → **410, "supprimée du serveur de
   réception"**. That is correct behaviour, not a defect.

### 7.3 Dossiers

Search by reference, surname, telephone, subject. Filter by statut,
urgence and **en retard**. Export the CSV and open it. Create a
**Nouveau dossier** (`QA-TEST …`): submit it empty first, then try a
future opening date — both must be refused.

### 7.4 One dossier, the whole fiche

| Section | Test |
| --- | --- |
| Statut | Only the offered moves work; make one; the history gains a line with your name and the time |
| Affectations | Add a student and a supervisor; add the same person twice → refused; end one → leaves the active list but stays on record |
| Échéances | Add a past date → red, and the dossier appears under "en retard"; mark it done |
| Documents | Upload a PDF; download it; try a `.exe` → refused |
| Analyse | Write and submit → "en attente de validation"; **try the status that requires validation → refused**; validate it → that status opens; try rewriting the validated analysis → refused |

### 7.5 Journal

Everything you just did must be listed, with who and when. Filter by
action and by account.

### 7.6 Comptes

Create `qa-test@registre.test`, sign in as it, disable it, try again.
Try disabling your own account → refused.

---

## 8. Cross-cutting

- **375 px, 768 px, 1440 px** on: home, a listing, a detail page, an
  admin listing, a dossier fiche.
- **Keyboard only**: tab through the main navigation and one form; focus
  must always be visible.
- **Console**: zero errors on a normal page.

---

## 9. Do not report these

| | Why |
| --- | --- |
| "Not secure" in the address bar | No domain yet; HTTPS is UAM's to provide |
| `Statut provisoire 01…13` | The Clinic has not supplied the real labels |
| The four registre profiles see the same thing | The permission matrix is awaited |
| No e-mail ever arrives | SMTP credentials are awaited |
| Body text stays French in EN | Only the interface is translated |
| Figures such as "500+" | Examples, to be confirmed by the Clinic |
| The content is invented | Required during development |
| A purged attachment returns 410 | That is the rule working |

---

## 10. The report

Produce it as text that can be pasted into `docs/QA-FINDINGS.md`.

```markdown
# QA findings — <date>

Environment: http://40.160.97.168:8080 · <browser and version>
Tested by: <who>

## Summary
<one paragraph: what works, what is broken, what is doubtful>

## Findings

### 1. <short title> — Bloquant | Majeur | Mineur | Remarque
- **Where:** <URL, which account>
- **Steps:** 1… 2… 3…
- **What happened:**
- **What should have happened:**
- **Screenshot:** <reference>
- **Reproducible:** yes / no / intermittent

### 2. …

## Pages opened
| Page | Result |
| --- | --- |
| / | ok |
| /actualites | ok |
| … | … |

## Broken links, missing images, console errors
| Page | What | Detail |

## Works, but reads badly
<wording, layout, confusing labels — kept apart from the defects>

## Not covered
<what was not reached, and why>
```

**Severity:** *Bloquant* — data lost, a page down, or someone sees what
they must not. *Majeur* — a feature does not work. *Mineur* — visual or
wording. *Remarque* — works, but would be better otherwise.

Write what happened, not what should be concluded from it. A finding
nobody can reproduce from your description is not yet a finding — say so
rather than dressing it up.
