# Tasks: refactor/rediseno-admin-cms

## Review Workload Forecast

| Field | Value |
|-------|-------|
| Estimated changed lines | 8,000–10,000 |
| 400-line budget risk | High |
| Chained PRs recommended | Yes |
| Suggested split | PR-1 (Foundation) → PR-2 (Forms/Tables) → PR-3 (Dashboard/Cleanup) |
| Delivery strategy | auto-chain |
| Chain strategy | feature-branch-chain |

Decision needed before apply: No
Chained PRs recommended: Yes
Chain strategy: feature-branch-chain
400-line budget risk: High

### Suggested Work Units

| Unit | Goal | Likely PR | Notes |
|------|------|-----------|-------|
| 1 | CSS foundation + layout + login migration | PR-1 | Base = feature/tracker branch. Creates all 8 CSS files, migrates layout/login/user-card templates, adds JS for theme/sidebar. Old `admin.css` stays. |
| 2 | Forms, tables, content templates migration | PR-2 | Base = PR-1 branch. Migrates entry, content-type, base-content, project, user, media templates + components. Creates `tables.css`. |
| 3 | Dashboard + cleanup + final verification | PR-3 | Base = PR-2 branch. Migrates dashboard and remaining components. Creates `dashboard.css`. Deletes `admin.css`. Runs final grep verification. |

---

## PR-1: Foundation + Layout + Login

### CSS Files — Create (6 files)

- **[ ] PR-1-01** Create `voracms/public/css/admin/root.css` with all `--va-*` design tokens: colors, spacing (8px scale), radii, shadows, transitions, typography, layout dimensions, z-index. Dark-mode defaults. No component selectors.
  - **Files**: `voracms/public/css/admin/root.css`
  - **Deps**: none
  - **Criteria**: `grep -c "va-"` returns tokens only. All hardcoded color/radius/spacing values are extracted as variables.
  - **Est**: 1 file, ~265 lines

- **[ ] PR-1-02** Create `voracms/public/css/admin/layout.css` with `.va-body`, `.va-sidebar`, `.va-sidebar__*`, `.va-overlay`, `.va-topbar`, `.va-topbar__*`, `.va-action-bar`, `.va-content`, `.va-footer`. Single responsive block for mobile sidebar toggle.
  - **Files**: `voracms/public/css/admin/layout.css`
  - **Deps**: PR-1-01
  - **Criteria**: All layout classes use `--va-*` tokens. Sidebar collapses outside viewport ≤1023px. Single `@media` block per rule (#17). Each class declared once (#18).
  - **Est**: 1 file, ~250 lines

- **[ ] PR-1-03** Create `voracms/public/css/admin/components.css` with `.va-btn`, `.va-btn--*`, `.va-badge`, `.va-badge--*`, `.va-card`, `.va-card__*`, `.va-alert`, `.va-alert--*`, `.va-modal`, `.va-modal__*`, `.va-segment`, `.va-segment__*`, `.va-status-panel`, `.va-status-option`, `.va-user-card`, `.va-user-card__*`.
  - **Files**: `voracms/public/css/admin/components.css`
  - **Deps**: PR-1-01
  - **Criteria**: All component styles use `--va-*` tokens. BEM structure enforced. Each class declared once. Single responsive block per class.
  - **Est**: 1 file, ~450 lines

- **[ ] PR-1-04** Create `voracms/public/css/admin/forms.css` with `.va-form`, `.va-form__*`, `.va-input`, `.va-select`, `.va-textarea`, `.va-check`, `.va-check__*`, `.va-upload`, `.va-upload__*`, `.va-gallery`, `.va-gallery__*`. Includes 2-column grid layout for document+meta sidebar, sticky meta panel.
  - **Files**: `voracms/public/css/admin/forms.css`
  - **Deps**: PR-1-01
  - **Criteria**: Input styles match Style 01 Soft Dark (`rgba(255,255,255,0.06)` bg). `.va-form__grid` toggles `1fr` / `1fr 260px` at 1024px. `.va-form__meta` sticky on desktop. Single responsive block per class.
  - **Est**: 1 file, ~500 lines

- **[ ] PR-1-05** Create `voracms/public/css/admin/login.css` with `.va-login`, `.va-login__card`, `.va-login__brand`, `.va-login__logo`, `.va-login__error`, `.va-login__footer`. Centered card with max-width, no sidebar/topbar.
  - **Files**: `voracms/public/css/admin/login.css`
  - **Deps**: PR-1-01
  - **Criteria**: `min-height: 100vh` centering. Error alert uses `--va-danger` tokens. References only `--va-*` variables.
  - **Est**: 1 file, ~80 lines

- **[ ] PR-1-06** Create `voracms/public/css/admin/theme.css` with single `[data-theme="light"]` block. Reassigns all surface, text, input, border, shadow, segment, and semantic background tokens for light mode. Zero `.va-*` selectors.
  - **Files**: `voracms/public/css/admin/theme.css`
  - **Deps**: PR-1-01
  - **Criteria**: `grep "\.va-"` returns 0. Only variable reassignments. Covers backgrounds, text, inputs, sidebar, segments, badges, alerts, shadows.
  - **Est**: 1 file, ~80 lines

### Templates — Modify (3 files)

- **[ ] PR-1-07** Modify `voracms/templates/admin/layout.html.twig`: migrate sidebar from `s-sidebar` → `va-sidebar` + BEM elements, topbar from `s-topbar`/`s-topbar-*` → `va-topbar`/`va-topbar__*`, content from `s-content` → `va-content`, footer from `s-footer` → `va-footer`, body from `s-body` → `va-body`. Add `va-overlay` with `data-action="sidebar-close"`. Add `va-topbar__theme-toggle` with `data-action="theme-toggle"`. Remove inline state controls. Add asset loading for all 8 admin CSS files. Update inline JS selectors to `va-*` classes.
  - **Files**: `voracms/templates/admin/layout.html.twig`
  - **Deps**: PR-1-01, PR-1-02, PR-1-03, PR-1-04, PR-1-05, PR-1-06
  - **Criteria**: Zero `s-*` classes in layout template. CSS loads 8 files. Theme toggle button present. Sidebar toggle data-action present. No inline style/onclick.
  - **Est**: 1 file, ~130 lines changed

- **[ ] PR-1-08** Modify `voracms/templates/admin/_user_card.html.twig`: migrate from `s-user-card` → `va-user-card`, `s-user-card-avatar` → `va-user-card__avatar`, `s-user-card-info` → `va-user-card__info`, `s-user-card-name` → `va-user-card__name`, `s-user-card-role` → `va-user-card__role`, `s-user-card-logout` → `va-user-card__logout`.
  - **Files**: `voracms/templates/admin/_user_card.html.twig`
  - **Deps**: PR-1-03
  - **Criteria**: Zero `s-*` classes. Avatar has explicit `width`/`height`. Logout link has `aria-label`.
  - **Est**: 1 file, ~17 lines

- **[ ] PR-1-09** Modify `voracms/templates/admin/login.html.twig`: migrate from `s-login`/`s-login-*`/Bootstrap classes (`.card`, `.card-body`, `.form-control`, `.form-label`, `.btn`, `.btn-primary`) to `va-login__*`, `va-input`, `va-btn--primary`, `va-alert--error`. Load only needed CSS: `root.css`, `forms.css`, `components.css`, `login.css`, `theme.css`.
  - **Files**: `voracms/templates/admin/login.html.twig`
  - **Deps**: PR-1-01, PR-1-03, PR-1-04, PR-1-05, PR-1-06
  - **Criteria**: Zero `s-*` classes. Zero Bootstrap form classes. Error toast uses `va-alert--error` with `role="alert"`. No inline styles.
  - **Est**: 1 file, ~52 lines

### JavaScript — Modify (1 file)

- **[ ] PR-1-10** Modify `voracms/public/js/admin.js`: update theme toggle selectors from `#themeToggle` → `[data-action="theme-toggle"]`. Update sidebar toggle from `.s-sidebar-toggle` / `.s-sidebar-overlay` → `[data-action="sidebar-toggle"]` / `[data-action="sidebar-close"]`. Update sidebar class from `.s-sidebar` → `.va-sidebar` and toggle `open` → `.va-sidebar--open`. Add `va-overlay--visible` class toggle. Ensure theme persists via `localStorage('voracms_theme')` with flash-free init before paint.
  - **Files**: `voracms/public/js/admin.js`
  - **Deps**: PR-1-07
  - **Criteria**: Theme toggle works without page reload. Sidebar opens/closes on mobile. Overlay click closes sidebar. `localStorage` saves and restores theme. No inline handlers used.
  - **Est**: 1 file, ~30 lines changed

### Template base — Modify (1 file)

- **[ ] PR-1-11** (Conditional) Modify `voracms/templates/base.html.twig` if any admin-specific CSS is loaded globally. Ensure admin CSS only loads via layout block, not base template.
  - **Files**: `voracms/templates/base.html.twig`
  - **Deps**: PR-1-07
  - **Criteria**: Base template does not reference `admin.css` or `css/admin/` files. Admin assets load only from `layout.html.twig`.
  - **Est**: 1 file, ~1 line (review only)

---

## PR-2: Forms + Tables + Content

### CSS — Create (1 file)

- **[ ] PR-2-01** Create `voracms/public/css/admin/tables.css` with `.va-table-wrapper`, `.va-table`, `.va-table__header`, `.va-table__body`, `.va-table__row`, `.va-table__row--inactive`, `.va-table__cell`, `.va-table__cell--actions`, `.va-table__actions`, `.va-table__sort`. Horizontal scroll on mobile via `overflow-x: auto` on wrapper.
  - **Files**: `voracms/public/css/admin/tables.css`
  - **Deps**: PR-1-01
  - **Criteria**: Header has distinct `--va-bg-table-header` background. Row hover uses `--va-bg-row-hover`. Wrapper scrolls on narrow viewports. All values from `--va-*` tokens. Single `@media` block per class.
  - **Est**: 1 file, ~200 lines

### Templates — Entry (4 files)

- **[ ] PR-2-02** Modify `voracms/templates/admin/entry/edit.html.twig`: migrate from Bootstrap `.card`/`.card-body`/`.row`/`.col-md-*`/`.form-control`/`.form-select`/`.btn` to `va-form`, `va-form__grid`, `va-form__document`, `va-form__meta`, `va-form__header`, `va-segment`, `va-segment__option`, `va-input`, `va-select`, `va-textarea`, `va-check`, `va-check__*`, `va-upload`, `va-gallery`, `va-btn`, `va-btn--primary`. Add segment control for status in header. Add sticky meta panel with `va-status-panel`. Replace inline state select.
  - **Files**: `voracms/templates/admin/entry/edit.html.twig`
  - **Deps**: PR-1-03, PR-1-04, PR-1-07, PR-2-01
  - **Criteria**: Zero `s-*`/`cyber-*`/Bootstrap form classes. 2-column grid for paired fields collapses on mobile. Status segment in header. Sticky meta panel on desktop. No inline style/onclick.
  - **Est**: 1 file, ~174 lines

- **[ ] PR-2-03** Modify `voracms/templates/admin/entry/new.html.twig`: same migration as edit, adapted for new-entry form.
  - **Files**: `voracms/templates/admin/entry/new.html.twig`
  - **Deps**: PR-1-03, PR-1-04, PR-1-07
  - **Criteria**: Zero `s-*`/`cyber-*`. Uses `va-form__*` layout. Field types render with correct `va-*` classes.
  - **Est**: 1 file, ~120 lines

- **[ ] PR-2-04** Modify `voracms/templates/admin/entry/show.html.twig`: migrate from Bootstrap cards to `va-card`, `va-card__*` and `va-badge` for status display.
  - **Files**: `voracms/templates/admin/entry/show.html.twig`
  - **Deps**: PR-1-03, PR-1-07
  - **Criteria**: Zero `s-*`/`cyber-*`. Status uses `va-badge`. Data display uses `va-card`.
  - **Est**: 1 file, ~80 lines

- **[ ] PR-2-05** Modify `voracms/templates/admin/entry/index.html.twig`: migrate from `cyber-table`/`cyber-row`/`cyber-cell`/`cyber-actions`/`cyber-btn`/`cyber-card` to `va-table`, `va-table__*`, `va-table__actions`, `va-btn`, `va-btn--*`, `va-card`. Migrate status display to `va-badge`. Empty state to `va-card` with centered content.
  - **Files**: `voracms/templates/admin/entry/index.html.twig`
  - **Deps**: PR-1-03, PR-1-07, PR-2-01
  - **Criteria**: Zero `cyber-*` classes. Table action buttons have `aria-label`. Badge uses `va-badge--*`. No inline styles.
  - **Est**: 1 file, ~148 lines

### Templates — Content-type (3 files)

- **[ ] PR-2-06** Modify `voracms/templates/admin/content-type/new.html.twig`, `edit.html.twig`, `index.html.twig`: migrate forms to `va-form__*`, `va-input`, `va-select`, `va-btn`. Migrate tables to `va-table__*`. Migrate cards to `va-card`.
  - **Files**: `voracms/templates/admin/content-type/new.html.twig`, `edit.html.twig`, `index.html.twig`
  - **Deps**: PR-1-03, PR-1-04, PR-1-07, PR-2-01
  - **Criteria**: Zero `s-*`/`cyber-*`/Bootstrap classes. Field builder rows use va-form grid. Buttons use `va-btn--*`. No inline handlers.
  - **Est**: 3 files, ~250 lines total

### Templates — Base-content (5 files)

- **[ ] PR-2-07** Modify `voracms/templates/admin/base-content/new.html.twig`, `edit.html.twig`, `show.html.twig`, `index.html.twig`, `_project_selector.html.twig`: migrate form/table/list views from old classes to `va-*`. Project selector to `va-select`, cards to `va-card`.
  - **Files**: `voracms/templates/admin/base-content/new.html.twig`, `edit.html.twig`, `show.html.twig`, `index.html.twig`, `_project_selector.html.twig`
  - **Deps**: PR-1-03, PR-1-04, PR-1-07, PR-2-01
  - **Criteria**: Zero `s-*`/`cyber-*`. Form fields use `va-input`, `va-select`, `va-textarea`. Tables use `va-table__*`. No inline style/onclick.
  - **Est**: 5 files, ~300 lines total

### Templates — Project (3 files)

- **[ ] PR-2-08** Modify `voracms/templates/admin/project/form.html.twig`, `index.html.twig`, `show.html.twig`: migrate forms to `va-form__*`, `va-input`, `va-btn`. Migrate project cards/lists to `va-card`, `va-table__*`. Migrate action buttons to `va-btn--*`.
  - **Files**: `voracms/templates/admin/project/form.html.twig`, `index.html.twig`, `show.html.twig`
  - **Deps**: PR-1-03, PR-1-04, PR-1-07, PR-2-01
  - **Criteria**: Zero `s-*`/`cyber-*`. Project show page uses `va-card` for details. Form uses document+meta layout. No inline handlers.
  - **Est**: 3 files, ~200 lines total

### Templates — User (2 files)

- **[ ] PR-2-09** Modify `voracms/templates/admin/user/form.html.twig`, `index.html.twig`: migrate form to `va-form__*`, `va-input`, `va-select`, `va-btn`. Migrate user table to `va-table__*` with `va-badge` for roles. Action buttons to `va-btn--*`.
  - **Files**: `voracms/templates/admin/user/form.html.twig`, `index.html.twig`
  - **Deps**: PR-1-03, PR-1-04, PR-1-07, PR-2-01
  - **Criteria**: Zero `s-*`/`cyber-*`. User table has role badges. Action buttons have `aria-label`. No inline handlers.
  - **Est**: 2 files, ~150 lines total

### Templates — Media (2 files)

- **[ ] PR-2-10** Modify `voracms/templates/admin/media/index.html.twig`, `picker.html.twig`: migrate grid cards from `cyber-card`/`s-media-*` to `va-card`, `va-gallery`. Migrate upload form to `va-upload`. Migrate buttons/modals to `va-btn`, `va-modal`. Keep Bootstrap modal for upload modal (Bootstrap JS dependency) but override with `va-*` tokens.
  - **Files**: `voracms/templates/admin/media/index.html.twig`, `picker.html.twig`
  - **Deps**: PR-1-03, PR-1-04, PR-1-07
  - **Criteria**: Zero `s-*`/`cyber-*` classes. Upload zone uses `va-upload__*` with dashed border. Thumbnail grid uses `va-gallery__*`. Media delete buttons have `aria-label`. No inline styles (remove inline `position:absolute` etc from gallery thumbs).
  - **Est**: 2 files, ~244 lines total

### Components — Migrate (7 files)

- **[ ] PR-2-11** Modify `voracms/templates/admin/components/_gallery_field.html.twig`: migrate from `gallery-field`/`gallery-previews`/`gallery-thumb`/`s-file-label`/Bootstrap to `va-gallery`, `va-gallery__grid`, `va-gallery__thumb`, `va-gallery__image`, `va-gallery__remove`, `va-upload`, `va-upload__*`. Remove inline styles from remove button (move to CSS via `.va-gallery__remove`). Add `data-role="gallery"` and `data-role="media-picker"` attributes.
  - **Files**: `voracms/templates/admin/components/_gallery_field.html.twig`
  - **Deps**: PR-1-03, PR-1-04, PR-1-07
  - **Criteria**: Zero inline `style=""`. Zero `s-*`/Bootstrap classes. Remove button has `aria-label` and styles from CSS. Uses `va-gallery__*` BEM.
  - **Est**: 1 file, ~26 lines

- **[ ] PR-2-12** Modify `voracms/templates/admin/components/_badge.html.twig`: migrate from `cyber-badge` → `va-badge`, `cyber-badge--*` → `va-badge--*`.
  - **Files**: `voracms/templates/admin/components/_badge.html.twig`
  - **Deps**: PR-1-03
  - **Criteria**: Uses `va-badge` and `va-badge--{modifier}`. Zero `cyber-*` classes.
  - **Est**: 1 file, ~2 lines

- **[ ] PR-2-13** Modify `voracms/templates/admin/components/_status.html.twig`: migrate from `cyber-status`/`cyber-status-*` to `va-badge`, `va-badge--success`/`va-badge--neutral` with dot element pattern.
  - **Files**: `voracms/templates/admin/components/_status.html.twig`
  - **Deps**: PR-1-03
  - **Criteria**: Zero `cyber-*` classes. Uses `va-badge` with dot indicator.
  - **Est**: 1 file, ~5 lines

- **[ ] PR-2-14** Modify `voracms/templates/admin/components/_toggle_btn.html.twig`: migrate from `cyber-btn`/`cyber-form--inline`/`cyber-btn--toggle-*` to `va-btn`, `va-btn--ghost`, `va-btn--icon` with `data-active` attribute. Update JS listeners to use `data-action="toggle-active"`.
  - **Files**: `voracms/templates/admin/components/_toggle_btn.html.twig`
  - **Deps**: PR-1-03, PR-1-07
  - **Criteria**: Zero `cyber-*` classes. Form uses `va-form--inline`. Button uses `va-btn--*`. JS listener delegates via data-action.
  - **Est**: 1 file, ~11 lines

- **[ ] PR-2-15** Modify `voracms/templates/admin/components/_section_header.html.twig`: migrate from `cyber-section-header`/`cyber-section-*`/`cyber-stat-mini` to `va-section-header`, `va-section-header__*`, `va-stat-mini`, `va-stat-mini__*`.
  - **Files**: `voracms/templates/admin/components/_section_header.html.twig`
  - **Deps**: PR-1-03, PR-1-07
  - **Criteria**: Zero `cyber-*` classes. BEM naming applied consistently.
  - **Est**: 1 file, ~25 lines

- **[ ] PR-2-16** Modify `voracms/templates/admin/components/_repeater_field.html.twig` and `_quill_field.html.twig`: migrate wrapper classes from `s-*`/`cyber-*` to `va-*`. Ensure Quill editor wrapper uses `va-form__field` and Quill-specific data attributes. Keep Quill JS init in entry-form.js, move inline styles to CSS.
  - **Files**: `voracms/templates/admin/components/_repeater_field.html.twig`, `_quill_field.html.twig`
  - **Deps**: PR-1-03, PR-1-04, PR-1-07
  - **Criteria**: Zero inline styles. Zero `s-*`/`cyber-*` classes. Quill toolbar and container use va-* wrappers. No inline JS event handlers.
  - **Est**: 2 files, ~50 lines total

### JavaScript — Update (1 file)

- **[ ] PR-2-17** Update `voracms/public/js/admin.js`: add delegated event listeners for segment control (`data-role="segment"`), gallery remove (`data-role="gallery"` + `.va-gallery__remove`), media picker trigger (`data-role="media-picker"`). Update toggle-AJAX from `cyber-btn--toggle-*` patterns to `va-btn--*` + `data-active`. Update `.cyber-row`/`.row-inactive` references to `va-table__row`/`va-table__row--inactive`. Update stop-propagation from `.cyber-cell--actions` to `.va-table__cell--actions`.
  - **Files**: `voracms/public/js/admin.js`
  - **Deps**: PR-2-02 through PR-2-16
  - **Criteria**: Segment control updates hidden input and `aria-selected` on click. Gallery remove removes thumb from DOM. Toggle AJAX updates `va-btn` classes and row state. No `cyber-*`/`s-*` class references remain in JS.
  - **Est**: 1 file, ~50 lines changed

### Preview Templates — Migrate (4 files)

- **[ ] PR-2-18** Modify `voracms/templates/admin/entry/preview.html.twig`, `preview_noticia.html.twig`, `preview_generic.html.twig`, `preview_event.html.twig`, `preview_artistes_victoria_taylor.html.twig`: migrate structural classes from `s-*`/`cyber-*` to `va-*`. These are iframe-rendered previews and need minimal CSS — keep them aligned with admin design system.
  - **Files**: `voracms/templates/admin/entry/preview.html.twig`, `preview_noticia.html.twig`, `preview_generic.html.twig`, `preview_event.html.twig`, `preview_artistes_victoria_taylor.html.twig`
  - **Deps**: PR-1-03, PR-1-07
  - **Criteria**: Zero `s-*`/`cyber-*` classes. Preview uses `va-card`, `va-badge` for status. No inline styles.
  - **Est**: 5 files, ~150 lines total

---

## PR-3: Dashboard + Components + Cleanup

### CSS — Create (1 file)

- **[ ] PR-3-01** Create `voracms/public/css/admin/dashboard.css` with `.va-stat-card`, `.va-stat-card__*`, `.va-metrics-grid`, `.va-project-row`, `.va-project-row__*`, `.va-list-card`, `.va-list-card__*`. Responsive grid: 4 cols at 1024px, 2 at 480px, 1 at default.
  - **Files**: `voracms/public/css/admin/dashboard.css`
  - **Deps**: PR-1-01
  - **Criteria**: All `--va-*` tokens. Single responsive block per class. Grid collapses gracefully. Stat cards have icon, value, label BEM structure.
  - **Est**: 1 file, ~180 lines

### Templates — Dashboard (1 file)

- **[ ] PR-3-02** Modify `voracms/templates/admin/dashboard.html.twig`: migrate stat cards from `s-stat-card` → `va-stat-card`, metric grid from Bootstrap `row col-md-*` → `va-metrics-grid`. Migrate list cards from `cyber-card--list`/`cyber-list-item`/`cyber-avatar` to `va-list-card`, `va-list-item`, `va-list-item__*`. Migrate project rows from `cyber-project-card-row`/`cyber-project-*` to `va-project-row`, `va-project-row__*`. Migrate action buttons to `va-btn--*`.
  - **Files**: `voracms/templates/admin/dashboard.html.twig`
  - **Deps**: PR-1-03, PR-1-07, PR-3-01
  - **Criteria**: Zero `s-*`/`cyber-*` classes. No Bootstrap layout classes. Stat cards use `va-stat-card__*`. Project rows use `va-project-row__*`. Color-coded status values use `--va-success`/`--va-warning` tokens. No inline styles.
  - **Est**: 1 file, ~248 lines

### Components — Dashboard (2 files)

- **[ ] PR-3-03** Modify `voracms/templates/admin/components/_dashboard_stat_card.html.twig`: migrate from `s-stat-card`/`s-stat-label`/`s-stat-value` to `va-stat-card`, `va-stat-card__icon`, `va-stat-card__value`, `va-stat-card__label`. Accept icon SVG name, not just Bootstrap icon class.
  - **Files**: `voracms/templates/admin/components/_dashboard_stat_card.html.twig`
  - **Deps**: PR-1-03, PR-3-01
  - **Criteria**: Zero `s-*` classes. Uses `va-stat-card__*` BEM. Accepts `icon`, `label`, `value`, optional `modifier` params.
  - **Est**: 1 file, ~8 lines

- **[ ] PR-3-04** Modify `voracms/templates/admin/components/_stat_mini.html.twig`: migrate from `cyber-stat-mini`/`cyber-stat-mini-*` to `va-stat-mini`, `va-stat-mini__value`, `va-stat-mini__label`.
  - **Files**: `voracms/templates/admin/components/_stat_mini.html.twig`
  - **Deps**: PR-1-03
  - **Criteria**: Zero `cyber-*` classes. Uses `va-stat-mini__*` BEM.
  - **Est**: 1 file, ~5 lines

### Templates — API Guide (1 file)

- **[ ] PR-3-05** Modify `voracms/templates/admin/api-guide.html.twig`: migrate layout and code blocks from `s-*`/`cyber-*` to `va-*`. Update card wrappers to `va-card`, code blocks to appropriate `va-*` containers.
  - **Files**: `voracms/templates/admin/api-guide.html.twig`
  - **Deps**: PR-1-03, PR-1-07
  - **Criteria**: Zero `s-*`/`cyber-*` classes. Code sections use `va-card` with pre-styled blocks. No inline styles.
  - **Est**: 1 file, ~80 lines

### Cleanup — Delete Old CSS

- **[ ] PR-3-06** Delete `voracms/public/css/admin.css` after confirming all templates are migrated. Update any remaining references in `base.html.twig` or other templates.
  - **Files**: `voracms/public/css/admin.css`
  - **Deps**: All PR-1, PR-2, PR-3 template migration tasks completed
  - **Criteria**: `test -f voracms/public/css/admin.css` fails. `grep -R "admin.css" voracms/templates/` returns 0.
  - **Est**: 1 file, ~6804 lines removed

### Verification — Final Checks

- **[ ] PR-3-07** Run final verification scripts:
  ```bash
  grep -R "class=\"[^\"]*s-\|class=\"[^\"]*cyber-" templates/admin/    # expect 0
  grep -R "style=\|onclick=\|onchange=\|onmouseover=" templates/admin/ # expect 0
  find public/css/admin/ -name "*.css" | wc -l                        # expect 8
  ```
  Manual visual check on 320px, 768px, 1440px for: dashboard, entry/edit, entry/index, login. Verify theme toggle persistence.
  - **Files**: All admin templates and CSS
  - **Deps**: PR-3-06
  - **Criteria**: Zero old-class hits. Zero inline styles/handlers. 8 CSS files exist. No visual regressions in both themes at all breakpoints.
  - **Est**: 5 min manual test

---

## Architecture Rules Enforcement

The following rules are cross-cutting and MUST be verified at every task:

- **Rule #17** (single media query per class): Each CSS file has one responsive block per class — verify during code review.
- **Rule #18** (single definition per class): Each `.va-*` selector appears exactly once across all 8 CSS files — grep after each PR.
- **Zero inline CSS**: `grep -R "style=" templates/admin/` must return 0 after each PR.
- **Zero inline JS handlers**: `grep -R "onclick=\|onchange=\|onmouseover=" templates/admin/` must return 0 after each PR.
- **Zero `s-*`/`cyber-*` classes**: `grep -R "class=\"[^\"]*s-\|class=\"[^\"]*cyber-" templates/admin/` returns 0 after PR-3.
