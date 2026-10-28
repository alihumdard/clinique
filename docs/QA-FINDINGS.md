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

### Not covered

Everything behind a sign-in: parts B through E, and the client-side half
of F. Pass 2 starts there, and starts with the registre separation.
