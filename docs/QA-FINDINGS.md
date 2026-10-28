# QA findings

Where test results are recorded, and what became of them.

One section per run, newest first. The test plans are
[QA-PLAN.md](QA-PLAN.md) for a run with the code, and
[QA-NAVIGATEUR.md](QA-NAVIGATEUR.md) for a run from the browser alone.

**Severity**

| | |
| --- | --- |
| **Bloquant** | Data lost, a page down, or someone sees what they must not |
| **Majeur** | A feature does not work; a workaround exists |
| **Mineur** | Visual, wording, an edge case |
| **Remarque** | Works, but would read better otherwise |

**Status** — `ouvert` · `en cours` · `corrigé (commit)` · `ne sera pas corrigé (raison)` · `non reproductible`

---

## Run of <date>

Environment: http://40.160.97.168:8080 · commit `…` · <browser>
Tested by: <who> · Plan followed: <QA-PLAN | QA-NAVIGATEUR>

### Summary

<One paragraph. What works, what is broken, what is doubtful.>

### Findings

| # | Finding | Severity | Where | Status |
| --- | --- | --- | --- | --- |
| 1 | | | | ouvert |

#### 1. <title>

- **Where:** <URL, which account>
- **Steps:** 1… 2… 3…
- **What happened:**
- **What should have happened:**
- **Screenshot:**
- **Reproducible:** yes / no / intermittent
- **Status:** ouvert
- **Fix:** <commit and one line on what was changed, once corrected>

### Pages opened

| Page | Result |
| --- | --- |
| | |

### Broken links, missing images, console errors

| Page | What | Detail |
| --- | --- | --- |
| | | |

### Works, but reads badly

<Wording, layout, confusing labels. Kept apart from the defects on
purpose: mixing the two is how a real defect ends up queued behind a
preference.>

### Not covered

<What was not reached, and why. A report that only lists what broke
leaves the reader unable to tell thoroughness from luck.>

---

## How a finding is closed

1. Reproduce it from the description alone. If that fails, it goes back
   as `non reproductible` with what was tried — not as a fix.
2. Write a test that fails for the reason stated.
3. Fix it, and make that test pass.
4. Record the commit against the finding above.

A finding closed without a test is a finding that will be reported
again.
