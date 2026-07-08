# Proposal: refactor/rediseno-admin-cms

## Intent

Unify two competing class systems (`s-*`, `cyber-*`, inline styles) into a single `va-*` (VoraAdmin) design system. Eliminate the 6,804-line `admin.css` monolith by splitting into 8 maintainable files. Implement a modern Linear/Notion-inspired visual design combining showroom styles 01 (Soft Dark inputs), 06 (Clean Light segment controls), and 10 (Notion gallery layout). Enforce project rules #17 (one media query per class) and #18 (one definition per class).

## Scope

### In Scope
- Rewrite ALL 6,804 lines of `public/css/admin.css` into 8 files under `public/css/admin/`
- Migrate ALL 39 admin Twig templates from `s-*`/`cyber-*`/inline styles to `va-*` classes
- Refactor sidebar, topbar, action-bar, content area, footer (`layout.html.twig`)
- Refactor all form inputs, selects, textareas, checkboxes, upload zones, galleries
- Refactor tables (list views, action buttons, row states)
- Refactor dashboard stat cards and metric widgets
- Refactor login page
- Refactor cards, badges, tabs, modals, media picker
- Theme system: dark default, light via `[data-theme=light]` variable overrides only
- Migrate JS inline event handlers (`onclick`, `onmouseover`) to external listeners in `admin.js`

### Out of Scope
- PHP/Controller business logic — no backend changes
- Symfony routing or form types — no backend refactors
- Public-facing site styles (VoraStudio, voraRaymel, aulaGastronomica) — admin only
- JS framework migration — stays vanilla JS

## Capabilities

### New Capabilities
- `va-design-system`: Unified `va-*` component library for all admin UI (forms, tables, cards, sidebar, buttons, badges, tabs, modals)
- `va-theme`: CSS custom property theme engine — dark default, light via `[data-theme=light]` variable overrides, zero selector duplication
- `va-form-composition`: Form layout system combining style 01 sidebar status grid + style 10 Notion document layout with sticky metadata panel

### Modified Capabilities
- `admin-layout`: Sidebar/topbar/content structure changes from `s-*` classes to `va-*` classes. No spec-level behavior change — pure CSS class rename + responsive improvements
- `admin-css-architecture`: Monolithic `admin.css` replaced with 8-file modular structure. CSS delivery strategy changes from single bundle to per-category file includes

## Approach

### Naming
- Prefix `va-` for all classes (e.g., `va-sidebar`, `va-input`, `va-table`, `va-btn`, `va-card`, `va-badge`)
- BEM modifiers: `va-btn--primary`, `va-input--error`, `va-card--dark`

### File Structure (8 files under `public/css/admin/`)
| File | Content |
|------|---------|
| `root.css` | Design tokens: colors, spacing (8px scale), radii, transitions, z-index. Extracted from current `root.css` `--s-*` → renamed to `--va-*` |
| `layout.css` | Sidebar, topbar, content area, footer, responsive breakpoints |
| `forms.css` | All inputs, selects, textareas, checkboxes, radio groups, upload zones, galleries |
| `components.css` | Cards, badges, tabs, modals, segment controls, action buttons, status pills |
| `tables.css` | Table grid, row states (active/inactive), action cells, sort headers |
| `dashboard.css` | Stat cards, metric grid, activity lists, chart containers |
| `login.css` | Login card, brand, form, error toast |
| `theme.css` | `[data-theme=light]` block — only variable reassignments, zero selectors |

### Design Reference Mapping
| Showroom Style | Applied To | Key Visual Tokens |
|----------------|------------|-------------------|
| **01 Soft Dark** | Form inputs, field backgrounds, status sidebar, action buttons | `rgba(255,255,255,0.06)` input bg, `9px` radius, `#141418` card bg, `#0f0f13` sidebar bg |
| **06 Clean Light** | Segment controls in topbar, light theme overrides for tabs | `#f0f0f0` segment bg, `8px` radius, `2px` padding, white selected state with shadow |
| **10 Notion-inspired** | Gallery layout, media area, breadcrumb title, sticky metadata side panel | `1fr 260px` grid, `#1c1c1f` panel bg, `15px` radius, sticky meta sidebar |

### Theme Strategy
```
:root { /* dark default */
  --va-bg-page: #0c0e24;
  --va-input-bg: rgba(255,255,255,0.06);
  --va-input-border: rgba(255,255,255,0.11);
  --va-text: rgba(255,255,255,0.92);
  /* ... 80+ tokens */
}

[data-theme=light] {
  --va-bg-page: #f5f5f7;
  --va-input-bg: #ffffff;
  --va-input-border: #e3e3e3;
  --va-text: #111111;
  /* only variable reassignments, no selectors */
}
```

### Rule #17 and #18 Enforcement
- **Rule #17** (one media query per class): During refactor, each class's responsive overrides are grouped into a single `@media` block at the bottom of each file. If a class needs changes at multiple breakpoints, they live in ONE `@media` block with nested selectors.
- **Rule #18** (one definition per class): Zero duplicate selectors. Each `va-*` class declared exactly once. Variations via BEM modifiers only.

### Migration Strategy
**Progressive two-phase**:
1. **Phase A — CSS foundation**: Write all 8 CSS files, deploy them alongside existing `admin.css`. Add `va-` prefixed classes to templates incrementally.
2. **Phase B — Template swap**: Batch-update all 39 templates in 3 ordered PRs: (1) Layout + Sidebar + Login, (2) Forms + Tables + Content types, (3) Dashboard + Components + Media. Remove old `admin.css` after verification.

## Affected Areas

| Area | Impact | Description |
|------|--------|-------------|
| `public/css/admin.css` | Removed | Replaced by 8 modular files |
| `public/css/root.css` | Modified | `--s-*` tokens renamed to `--va-*` |
| `public/css/admin/root.css` | New | Design tokens |
| `public/css/admin/layout.css` | New | Layout system |
| `public/css/admin/forms.css` | New | Form controls |
| `public/css/admin/components.css` | New | UI components |
| `public/css/admin/tables.css` | New | Table system |
| `public/css/admin/dashboard.css` | New | Dashboard widgets |
| `public/css/admin/login.css` | New | Login page |
| `public/css/admin/theme.css` | New | Light theme overrides |
| `templates/admin/layout.html.twig` | Modified | `s-*` → `va-*`, inline JS → external |
| `templates/admin/login.html.twig` | Modified | `s-*` → `va-*` |
| `templates/admin/dashboard.html.twig` | Modified | `s-*` → `va-*`, inline handlers → listeners |
| `templates/admin/entry/*.html.twig` | Modified | 4 templates: `s-*` → `va-*` |
| `templates/admin/content-type/*.html.twig` | Modified | 3 templates |
| `templates/admin/base-content/*.html.twig` | Modified | 2 templates |
| `templates/admin/media/*.html.twig` | Modified | 2 templates |
| `templates/admin/project/*.html.twig` | Modified | 3 templates |
| `templates/admin/user/*.html.twig` | Modified | 3 templates |
| `templates/admin/_user_card.html.twig` | Modified | Single component partial |
| `templates/base.html.twig` | Modified | CSS asset paths |
| `public/js/admin.js` | Modified | Inline JS extraction + new event bindings |

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| CSS regressions across 39 templates due to class rename | High | Ship Phase A CSS alongside old CSS; use `[class*="va-"]` specificity control; visual diff after each PR |
| Inline JS extraction breaks Quill/gallery/postMessage flows | Medium | Extract Quill init, gallery, and media picker as named functions in `admin.js`; keep inline only as bootstrapping calls |
| Light theme visual gaps in untested templates | Medium | `theme.css` uses variable overrides only — if a component misses a variable, it inherits dark default (safe fallback) |
| 6,800-line refactor exceeds review capacity | High | Use 3 chained PRs (see below); each under 400 changed lines in templates, ~800 in CSS |

## Rollback Plan

1. Keep `admin.css` and all old `s-*` classes in the codebase until Phase B is fully verified.
2. Each PR deploys independently. If a PR causes issues, revert only that PR — the CSS system remains backward-compatible because old classes are never removed in Phase A.
3. Final cleanup PR (removing `admin.css`) is the only irreversible step. Gate it behind 24h of production monitoring.

## Dependencies

- Prior or parallel `sdd/refactor-inline-css-js` exploration — inline JS extraction should happen before or alongside template class rename to avoid touching templates twice
- `voracms/public/js/admin.js` must exist with Quill, gallery, and toggle functions before templates drop inline scripts

## Success Criteria

- [ ] Zero `s-*` or `cyber-*` classes remain in any Twig template (grep returns zero hits)
- [ ] Zero inline `style=""` attributes in admin templates
- [ ] Zero inline `onclick`/`onmouseover`/`onmouseout` handlers in admin templates
- [ ] `admin.css` (6,804 lines) is removed, replaced by 8 files totaling < 5,000 lines
- [ ] Every `va-*` class is declared exactly once in the codebase (enforced by grep + manual review)
- [ ] Light theme toggle works on all 39 templates without visual breakage
- [ ] All 10 showroom form fields (title, subtitle, description, date, location, image, date range, gallery, status, actions) render correctly with `va-*` classes in both themes
- [ ] No visual regression on 320px, 768px, 1440px viewports for any admin page

## Review Workload Forecast

| Metric | Value |
|--------|-------|
| Current `admin.css` | 6,804 lines / 153 KB |
| New CSS total (estimated) | ~4,500 lines across 8 files (-34%) |
| Templates to modify | 39 files |
| Estimated total diff | ~8,000–10,000 lines (CSS + Twig + JS) |
| Recommended PR strategy | **3 chained PRs**: (1) CSS foundation + Layout/Sidebar/Login, (2) Forms/Tables/Content entries, (3) Dashboard/Components/Media + cleanup |

**Review recommendation**: 3 chained PRs. Each PR under 400 reviewable lines. PR #1 is the highest risk (new CSS architecture + first template migration). PR #2 and #3 are mechanically repetitive — class renaming with well-defined patterns.
