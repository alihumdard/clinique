# Master prompt — browser QA of the live platform

Paste the block below into a browser agent (Claude in Chrome, or any agent
that drives a real browser). It is self-contained: an agent that cannot
read this repository has everything it needs.

`QA-NAVIGATEUR.md` next to this file is the long form of the same plan, for
a human tester. This file is the prompt.

Keep the two in step. When a finding here is fixed, move it out of
*Already found* and into the record in `QA-FINDINGS.md`.

---

```text
PROJECT

Clinique Juridique FSJP-UAM — the digital platform of the legal clinic of
the Faculté des Sciences Juridiques et Politiques, Université Abdou
Moumouni, Niamey, Niger. The whole interface is French; an English
interface translation exists behind a flag switcher.

Built to a client specification in four modules. Modules 1–3 are complete
and live; module 4 (security, QA, documentation) is complete apart from
infrastructure that belongs to the university.

Stack: Laravel 12, PHP 8.4, Blade, Tailwind CSS 4, Vite 7, MySQL, nginx.
No remote scripts of any kind — no jQuery, no hosted font, no icon font,
no chart library, no analytics, no translation widget. That is a
requirement of the specification, enforced by a Content-Security-Policy of
script-src 'self'. Do not report the absence of a framework.

THREE AREAS, SEPARATE BY DESIGN

1. Public site — 35 routes, no sign-in. Institutional pages, news,
   activities, gallery, resources (PDF downloads), Web TV, and a
   four-step "pré-saisine" intake journey.
2. Administration (CMS) — /admin — 20 screens, nine content types, two
   roles (administrateur, éditeur).
3. Registre numérique — /registre — 13 screens, confidential case files.
   Its own database, its own accounts, its own sign-in.

The separation of 2 and 3 is the highest-risk thing on the platform. A CMS
account must get nothing in the registre, and the reverse. Test it first
and test it deliberately.

URL

https://clinique.40-160-97-168.sslip.io

Real Let's Encrypt certificate. The older http://40.160.97.168:8080 still
answers and is the same application — use the HTTPS name.

This is a live demonstration environment holding fictitious data, and the
client is testing the same address at the same time. The section CONDUCT
below is not optional.

CREDENTIALS

Administration — /admin/login
  admin@clinique-fsjp.ne   Administrateur   password supplied separately

  This is the only CMS account on the live server. Do not change its
  password, and do not disable it — it is the last administrator, and the
  platform refuses that anyway.

  There is no éditeur account yet. You create one in part D, as
  qa-test-editeur@example.test; I will type its password for you.

Registre — /registre/connexion       all four: registre-demo-2026
  coordination@registre.test     Coordinateur
  encadrant@registre.test        Superviseur / encadrant
  etudiant@registre.test         Étudiant clinicien
  technique@registre.test        Administrateur technique
  compte-desactive@registre.test DISABLED — signing in must fail

SIGNING IN

If your operating rules forbid you from typing credentials on this host —
it is a public HTTPS name, not localhost and not a *.test host — do not
work around it. Say which account you need and let me sign in; the session
cookie persists in the tab and you drive every screen after that. Ask for
them in this order so nothing waits on me twice:

  1. coordination@registre.test     (part E, highest risk, do this first)
  2. admin@clinique-fsjp.ne         (parts C and D)
  3. qa-test-editeur@example.test   (part D, once you have created it)
  4. etudiant@registre.test         (part E, the second profile)

The same applies to any password field in a form you fill: set up the
whole record, leave the password empty, and hand it to me.

Everything in parts A, B and the public half of F needs no sign-in at all
— start there while you wait.

SCOPE, in this order

A. Public site — all 35 routes, every link one level deep, every image,
   the FR/EN switcher, search, filters, pagination, the 404 page.
   Open two different articles and two different Web TV capsules: each
   must show its own content. A detail page that serves the same item to
   every link is exactly the defect this catches.

B. Public forms, no sign-in needed —
   - contact: empty, malformed e-mail, valid, then four in a row (the
     fourth must be refused by the rate limit);
   - newsletter: subscribe, then subscribe again with the same address;
   - pré-saisine: each of the four orientation exits separately from a
     fresh start (urgence = oui → emergency page; avocat = oui → ANAJJ
     referral; domaine = non → out of scope; déplacement = non → Niamey
     only), then the full journey — empty submit, a .exe attachment, six
     attachments, a file over 5 Mo (all refused), then a valid PDF, the
     review page, "corriger ma demande" returning your data intact, and
     finally a reference PS-YYYY-NNNN.
     Write that reference down. You will find it again in part E.
     Then open /pre-saisine/formulaire directly in a new tab: it must
     send you back to the orientation.

C. Administration — the full create / read / update / delete cycle on all
   nine content types: actualités, Web TV, fiches pratiques, rapports
   annuels, pages, appels à candidatures, activités & caravanes, équipe,
   partenaires. For each one: the "Ajouter" dialog (centred, scrolls, no
   blank band, closes on Escape and on the backdrop), an empty submit, a
   create with an image, then — this is the part that matters — go to the
   public site and confirm it is there, with its image, in the right
   place. Then edit, duplicate, delete (gone from the public site),
   restore (back on it).
   Publication states on at least three types: brouillon invisible even
   by direct URL; programmé for tomorrow invisible; published yesterday
   visible; archivé invisible.
   Also: référentiels (a fee, a domain, a category — each must show on
   the public page that reads it), paramètres (note the value, change the
   telephone, check header / footer / contact, put it back), messages,
   the newsletter CSV export and its accents.

D. Roles — create the éditeur first, from /admin/utilisateurs, as
   qa-test-editeur@example.test, then sign in as them. The
   "Administration" group must be
   absent from the sidebar; /admin/parametres and /admin/utilisateurs
   must both answer "Accès refusé" when typed into the address bar; and
   creating an actualité must still work. Back as administrateur: a
   password under 12 characters is refused, a duplicate address is
   refused, disabling your own account is refused, and demoting the last
   administrator is refused.

E. Registre — separation first:
   - signed into the CMS, open /registre → the REGISTRE sign-in, not the
     CMS one;
   - signed into the registre, open /admin → the CMS sign-in;
   - compte-desactive@registre.test → sign-in fails;
   - five wrong passwords → temporarily locked.
   Then the pré-saisine from part B: find it under Demandes en ligne,
   open it, download the attachment, accept it → a dossier CJ-YYYY-NNNN
   is created → download the same attachment again → 410, "supprimée du
   serveur de réception". That 410 is the rule working. On a second
   submission, refuse it with a reason: no dossier, attachments gone.
   Then dossiers: search by reference, surname, telephone and subject;
   filter by statut, urgence and "en retard"; export the CSV; create a
   nouveau dossier (empty submit refused, a future opening date refused).
   Then one dossier end to end: a status move (only offered moves work,
   and the history gains a line with your name and the time);
   affectations (add a student and a supervisor, the same person twice
   refused, ending one leaves the active list but stays on record);
   échéances (a past date shows red and the dossier appears under "en
   retard"); documents (upload a PDF, download it, a .exe refused);
   analyse (write and submit → "en attente de validation"; the status
   that requires validation is refused until a supervisor validates;
   rewriting a validated analysis is refused).
   Then the journal: every one of those actions must be listed, with who
   and when. Then comptes: create one, sign in as it, disable it, try
   again; disabling your own account is refused.

F. Cross-cutting — 375 px, 768 px and 1440 px on the home page, a
   listing, a detail page, an admin listing and a dossier fiche; keyboard
   only through the main navigation and one form, with focus always
   visible; the console on a normal page; page weight against a budget of
   500 Ko for the home page and 400 Ko for inner pages.

CONDUCT on a live site the client is using

1. Prefix everything you create with QA-TEST. A news item is
   "QA-TEST actualité 1", a dossier subject "QA-TEST licenciement".
2. Delete what you created, at the end. Say what you could not delete.
3. Never touch a row you did not create. The seeded articles, dossiers
   and accounts are the client's demonstration data.
4. Never use the "définitif" action — permanent delete — on anything that
   was already there.
5. Restore any setting you change. Note the original value first.
6. Do not change the password of an account you were lent.
7. Two concurrent requests at most. There is no time window to avoid —
   throttling is enough. Saturating the connection pool produces
   measurements that look like site defects and are not.

MEASUREMENT DISCIPLINE

Separate what you measured from what you concluded, and name which one
you are reporting.

Response time in particular: a browser's time-to-first-byte contains the
round trip to the server, and this server is far from wherever you are
running. Measured on the machine itself, over TLS on the loopback, the
application renders / in 42 ms, /actualites in 42–52 ms and
/services/processus-de-consultation in 29 ms. APP_DEBUG is false, APP_ENV
is production, the config and route caches are warm, and OPcache is on
with 128 Mo. So a one- or two-second TTFB from your location is network,
and a TCP connect of 900 ms in your own numbers says so before PHP is
reached at all. Report latency as a measurement of the path, not as an
application defect — and say where you measured from.

The same discipline applies to anything intermittent. If you cannot
reproduce it deliberately, write that, or leave it out.

JUST FIXED — check these rather than hunt for them

A first pass over the public site found these; they are fixed and
deployed. Confirm each, and say so if one is not actually fixed.

- Paginated and filtered listings now canonicalise to themselves.
  /actualites?page=2 should declare itself, not /actualites. Campaign
  parameters (utm_*, fbclid, gclid, ref) should be absent from the
  canonical, and ?a=1&b=2 and ?b=2&a=1 should give one identical
  canonical.
- Titles now vary. Page two of a listing carries "(page 2)", and
  /actualites, /activites, /galerie and /rejoindre name the active
  rubrique when one is selected.
- The document declares the language actually being served. In English,
  <html lang="en"> and og:locale en_US; in French, fr. This was wrong
  before — every page claimed French.
- Heading order no longer skips a level anywhere on the public site,
  including /actualites and /politique-confidentialite, and including a
  gallery with no albums in it.
- Static assets carry exactly one Cache-Control header.
- /ressources redirects with 301.
- The contact page icon has width and height.

There are deliberately still no hreflang alternates. The English is an
interface translation at the same URL and the body text stays French;
alternates would advertise two addresses that are one address. Do not
report their absence.

Response time was reported as a major defect in that pass and was not one
— see MEASUREMENT DISCIPLINE above before raising it again.

NOT DEFECTS — intentional, and awaited from the client

- Dossier statuses read "Statut provisoire 01…13". The client has not
  approved the thirteen labels and the specification forbids inventing
  them. Statuses and their transitions are database rows; the supervisor
  gate is a flag on a row. No status is named anywhere in the code.
- All four registre profiles see the same screens. The permission matrix
  is awaited. Roles are recorded and every action is journalised.
- No e-mail ever arrives — contact, pré-saisine acknowledgement, none.
  SMTP credentials are awaited. Submissions are still saved and visible.
- In English, page body text stays French. Only the interface is
  translated; the body copy is the Clinic's own words and they will
  translate it.
- Figures such as "500+ personnes accompagnées" are placeholders.
- All content is fictitious. The specification requires that during
  development.
- A pré-saisine attachment returns 410 once the demande has been accepted.
  The file is deliberately purged from the intake server.
- Mentions légales says "Hébergeur : à confirmer".
- Rapports annuels have a listing and a download but no detail page. A
  report is a PDF; there is nothing to put on a detail page.
- An empty alt="" on a card thumbnail whose heading link carries the text
  is correct practice, not a missing alt.
- /ressources redirecting to /ressources/fiches-pratiques is deliberate;
  only the status code is wrong, and it is already listed above.

WHAT TO PRODUCE

One document, as markdown I can paste into docs/QA-FINDINGS.md:

  # QA findings — <date>
  Environment: <URL> · <browser and version> · signed in as <accounts>

  ## Summary
  One paragraph: what works, what is broken, what is doubtful.

  ## Findings
  Worst first. For each:
  - Where: URL, and which account
  - Steps: 1… 2… 3…
  - What happened:
  - What should have happened:
  - Severity: Bloquant | Majeur | Mineur | Remarque
  - Screenshot:
  - Reproducible: yes / no / intermittent

  ## Pages opened
  A table of every URL with its result, so that what was NOT covered is
  visible.

  ## Broken links, missing images, console errors
  Each tied to the page it appeared on.

  ## Accessibility
  Contrast, keyboard, focus, labels, alt text, heading order, landmarks.

  ## Performance
  Load times with where you measured from, render-blocking resources,
  image and font weight, LCP / CLS / INP, caching and compression.

  ## Works, but reads badly
  Wording, layout, confusing labels — kept apart from the defects.

  ## Not covered, and why

  ## Cleanup
  What you created and deleted; anything left behind.

SEVERITY

  Bloquant  data lost, a page down, or someone sees what they must not
  Majeur    a feature does not work; a workaround may exist
  Mineur    visual, wording, an edge case
  Remarque  works, but would be better otherwise

Write what happened, not what should be concluded from it. A finding
nobody can reproduce from your description is not yet a finding — say so
rather than dressing it up. If a whole part of the scope is blocked, say
which and why, and finish the rest.
```
