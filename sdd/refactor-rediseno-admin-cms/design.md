# Design: refactor/rediseno-admin-cms

## Technical Approach

Replace the monolithic `public/css/admin.css` and the fragmented `s-*` / `cyber-*` class systems with a single `va-*` (VoraAdmin) design system composed of eight focused stylesheets. The visual language merges three showroom references:

- **Style 01 Soft Dark** — form inputs, status panels, and sidebar state surfaces.
- **Style 06 Clean Light** — segment controls (status tabs) in form headers.
- **Style 10 Notion-inspired** — document + sticky metadata panel layout for edit forms.

Dark mode is the default (`:root`). Light mode is enabled only by `html[data-theme="light"]` and is implemented exclusively through variable reassignment in `theme.css`, with zero duplicated component selectors. All classes follow BEM under the `va-` prefix, each class is declared exactly once, and each class's responsive behavior lives in a single media-query block.

## Architecture Decisions

| Decision | Choice | Alternatives Rejected | Rationale |
|----------|--------|----------------------|-----------|
| Class prefix | `va-*` | Keep `s-*` / `cyber-*` | The user mandated a single unified library; `va-` is unambiguous and scoped to VoraAdmin. |
| CSS split | 8 files under `public/css/admin/` | One large file or per-page files | Mirrors the component categories (layout, forms, tables, etc.) and keeps each file under reviewable size. |
| Token file | `public/css/admin/root.css` with `--va-*` | Extend `public/css/root.css` | Admin tokens are numerous and themable; isolating them prevents leaking admin-only variables to the public site. |
| Theme engine | CSS custom properties + `[data-theme="light"]` | Separate light stylesheets or class-based themes | Guarantees zero selector duplication and instant theme switching without reloading assets. |
| Form layout | CSS Grid `1fr 260px` document + sticky sidebar | Flexbox or Bootstrap columns | Grid gives precise control over the Notion-style panel; sticky behavior is declarative and robust. |
| Responsive strategy | Mobile-first, one media query per class | Multiple breakpoint files or scattered overrides | Satisfies Rule #17 and makes specificity predictable. |
| JS event binding | External listeners in `admin.js` | Inline `onclick` / `onchange` | Satisfies Rule #803/REQ-804 and keeps HTML cacheable. |
| Bootstrap coexistence | Keep Bootstrap 5 for structural utilities; override visually with `va-*` | Remove Bootstrap | Rewriting the grid system is out of scope; Bootstrap classes (`row`, `col-md-*`) remain for layout scaffolding only. |

## Data Flow

```
base.html.twig
    └── reset.css, root.css, Bootstrap
    └── layout.html.twig
            └── admin/root.css … admin/login.css, admin/theme.css
            └── admin.js (theme toggle, sidebar, gallery, segments)
            └── Twig blocks: page_title, topbar_actions, content, javascripts
```

The theme toggle writes the chosen theme to `localStorage` and sets `document.documentElement.dataset.theme` synchronously before paint to avoid a flash. All components read colors from `--va-*` variables; `theme.css` only reassigns those variables when `[data-theme="light"]` is present.

## File Changes

| File | Action | Description |
|------|--------|-------------|
| `public/css/admin/root.css` | Create | Full `--va-*` token system (colors, spacing, radii, shadows, transitions, typography, layout, z-index). |
| `public/css/admin/layout.css` | Create | `va-body`, `va-sidebar`, `va-topbar`, `va-content`, `va-footer`, mobile sidebar toggle. |
| `public/css/admin/forms.css` | Create | `va-form`, `va-input`, `va-select`, `va-textarea`, `va-checkbox`, `va-radio`, `va-upload`, `va-gallery`, labels, help text. |
| `public/css/admin/components.css` | Create | `va-card`, `va-btn`, `va-badge`, `va-segment`, `va-status-panel`, `va-alert`, `va-modal`, `va-user-card`. |
| `public/css/admin/tables.css` | Create | `va-table`, `va-table__header`, `va-table__row`, `va-table__actions`, responsive wrapper. |
| `public/css/admin/dashboard.css` | Create | `va-stat-card`, `va-metrics-grid`, dashboard list cards, project rows. |
| `public/css/admin/login.css` | Create | `va-login`, centered card, error alert, brand. |
| `public/css/admin/theme.css` | Create | Single `[data-theme="light"]` block reassigning `--va-*` variables. |
| `public/css/admin.css` | Delete | After all templates are migrated and verified. |
| `public/js/admin.js` | Modify | Add theme toggle, sidebar toggle, segment-control binding, gallery/media picker event delegation. |
| `templates/admin/layout.html.twig` | Modify | `s-*` → `va-*`, load 8 CSS files, remove inline state controls, add `va-topbar__theme-toggle`. |
| `templates/admin/login.html.twig` | Modify | `s-*` → `va-*`, load only needed admin CSS (root, forms, components, login, theme). |
| `templates/admin/entry/*.html.twig` | Modify | Migrate forms, gallery, status segments, action buttons. |
| `templates/admin/content-type/*.html.twig` | Modify | Migrate form fields, field-builder rows, buttons. |
| `templates/admin/base-content/*.html.twig` | Modify | Migrate form and list views. |
| `templates/admin/project/*.html.twig` | Modify | Migrate project cards, forms, metrics. |
| `templates/admin/user/*.html.twig` | Modify | Migrate user table, forms. |
| `templates/admin/media/*.html.twig` | Modify | Migrate media grid, picker modal, upload zones. |
| `templates/admin/dashboard.html.twig` | Modify | Migrate stat cards, metric grids, project rows. |
| `templates/admin/components/_*.html.twig` | Modify | Migrate gallery, repeater, quill, stat card, badge, status, toggle, section header. |
| `templates/admin/api-guide.html.twig` | Modify | Migrate code blocks and layout. |
| `templates/admin/_user_card.html.twig` | Modify | `s-user-card` → `va-user-card`. |
| `templates/base.html.twig` | Modify | Ensure no admin-specific CSS is loaded globally; layout block handles admin assets. |

## Interfaces / Contracts

### CSS Variable Contract

All visual values MUST be expressed through `--va-*` tokens. No component file may contain raw hex/rgba literals that are part of the design system. Component files only reference variables declared in `root.css` (or reassigned in `theme.css`).

### BEM Contract

- Block: `.va-block`
- Element: `.va-block__element`
- Modifier: `.va-block--modifier` or `.va-block__element--modifier`
- State hooks for JS use `data-*` attributes or `aria-*`, never classes like `.active` alone. CSS may use `.va-block--active` or `[aria-selected="true"]`.

### JS Contracts

- Theme toggle: `data-action="theme-toggle"` on `va-topbar__theme-toggle`.
- Sidebar toggle: `data-action="sidebar-toggle"` on `va-topbar__menu-toggle`.
- Segment control: `data-role="segment"` with `data-value` buttons; JS updates hidden input and `aria-selected`.
- Gallery: `data-role="gallery"` container, `data-field-id`, `.va-gallery__thumb`, `.va-gallery__remove`.
- Media picker: `data-role="media-picker"`, `data-field`, `data-multiple`.

## Testing Strategy

| Layer | What to Test | Approach |
|-------|-------------|----------|
| Static | Zero `s-*` / `cyber-*` classes in `templates/admin/` | `grep -R "class=\"[^\"]*s-\|class=\"[^\"]*cyber-" templates/admin/` |
| Static | Zero inline styles / handlers | `grep -R "style=\|onclick=\|onchange=\|onmouseover=" templates/admin/` |
| Static | One definition per class | `grep -c "\.va-"` per file; manual review for duplicates |
| Static | One media query per class | Visual inspection of each file's single `@media` block per selector |
| Static | Theme file purity | `theme.css` contains only `[data-theme="light"]` and variable reassignments |
| Visual | 320px / 768px / 1440px | Manual browser testing on key pages: dashboard, entry/edit, entries list, login |
| Functional | Theme persistence | Toggle theme, navigate, reload; verify `localStorage` key and no flash |
| Functional | Sidebar mobile | Toggle open/close and overlay click on iPhone SE viewport |

## Migration / Rollout

Progressive two-phase rollout via three chained PRs:

1. **PR-1 — Foundation + Layout + Login**
   - Create all 8 CSS files.
   - Update `layout.html.twig`, `login.html.twig`, `_user_card.html.twig`.
   - Update `base.html.twig` if needed.
   - Add JS for theme toggle and sidebar.
   - Keep old `admin.css` in place.

2. **PR-2 — Forms + Tables + Content**
   - Migrate `entry/*.twig`, `content-type/*.twig`, `base-content/*.twig`, `project/*.twig`, `user/*.twig`.
   - Migrate reusable components: `_gallery_field`, `_repeater_field`, `_quill_field`, `_badge`, `_status`.
   - Migrate `media/*.twig`.

3. **PR-3 — Dashboard + Cleanup**
   - Migrate `dashboard.html.twig`, `_dashboard_stat_card`, `_stat_mini`, `_section_header`.
   - Migrate `api-guide.html.twig` and any remaining partials.
   - Delete `public/css/admin.css`.
   - Final verification grep scripts.

---

# 1. Token System (`public/css/admin/root.css`)

## 1.1 Colores

```css
:root {
  /* Brand */
  --va-primary: #4945ff;
  --va-primary-hover: #3a36e0;
  --va-primary-light: #eeedff;
  --va-primary-bg: rgba(73, 69, 255, 0.10);

  /* Page surfaces (dark default) */
  --va-bg-page: #0c0e24;
  --va-bg-page-gradient-start: #080a18;
  --va-bg-page-gradient-mid: #0b0d22;
  --va-bg-page-gradient-end: #0d0f28;
  --va-bg-card: #141418;
  --va-bg-card-hover: #1a1a1f;
  --va-bg-panel: #1c1c1f;
  --va-bg-elevated: #1f1f23;

  /* Sidebar */
  --va-sidebar-bg: #0f0f13;
  --va-sidebar-border: rgba(255, 255, 255, 0.07);
  --va-sidebar-text: rgba(255, 255, 255, 0.72);
  --va-sidebar-text-hover: #ffffff;
  --va-sidebar-hover: rgba(255, 255, 255, 0.06);
  --va-sidebar-active: rgba(73, 69, 255, 0.18);
  --va-sidebar-active-accent: var(--va-primary);
  --va-sidebar-section: rgba(255, 255, 255, 0.35);

  /* Text */
  --va-text: rgba(255, 255, 255, 0.92);
  --va-text-secondary: rgba(255, 255, 255, 0.65);
  --va-text-muted: rgba(255, 255, 255, 0.45);
  --va-text-placeholder: rgba(255, 255, 255, 0.22);
  --va-text-disabled: rgba(255, 255, 255, 0.30);

  /* Inputs (Style 01 Soft Dark) */
  --va-input-bg: rgba(255, 255, 255, 0.06);
  --va-input-bg-focus: rgba(255, 255, 255, 0.09);
  --va-input-bg-disabled: rgba(255, 255, 255, 0.02);
  --va-input-border: rgba(255, 255, 255, 0.11);
  --va-input-border-hover: rgba(255, 255, 255, 0.18);
  --va-input-border-focus: rgba(255, 255, 255, 0.30);
  --va-input-color: var(--va-text);

  /* Segment control (Style 06 Clean Light, adapted) */
  --va-segment-bg: rgba(255, 255, 255, 0.08);
  --va-segment-selected: rgba(255, 255, 255, 0.14);
  --va-segment-text: var(--va-text-muted);
  --va-segment-text-selected: var(--va-text);
  --va-segment-shadow: none;

  /* Status dots */
  --va-status-draft: rgba(255, 255, 255, 0.35);
  --va-status-published: #4caf50;
  --va-status-archived: #ffa726;

  /* Semantic */
  --va-success: #4caf50;
  --va-success-bg: rgba(76, 175, 80, 0.12);
  --va-warning: #ffa726;
  --va-warning-bg: rgba(255, 167, 38, 0.12);
  --va-danger: #ef5350;
  --va-danger-bg: rgba(239, 83, 80, 0.12);
  --va-info: #06b6d4;
  --va-info-bg: rgba(6, 182, 212, 0.12);

  /* Borders */
  --va-border-subtle: rgba(255, 255, 255, 0.06);
  --va-border-default: rgba(255, 255, 255, 0.08);
  --va-border-strong: rgba(255, 255, 255, 0.12);

  /* Shadows */
  --va-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.20);
  --va-shadow-md: 0 4px 16px rgba(0, 0, 0, 0.24);
  --va-shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.32);
  --va-shadow-xl: 0 24px 64px rgba(0, 0, 0, 0.40);
  --va-shadow-segment-selected: none;

  /* Radii */
  --va-radius-sm: 6px;
  --va-radius-md: 9px;
  --va-radius-lg: 12px;
  --va-radius-xl: 15px;
  --va-radius-2xl: 16px;
  --va-radius-pill: 999px;

  /* Spacing (8px scale) */
  --va-space-1: 4px;
  --va-space-2: 8px;
  --va-space-3: 12px;
  --va-space-4: 16px;
  --va-space-5: 20px;
  --va-space-6: 24px;
  --va-space-7: 28px;
  --va-space-8: 32px;
  --va-space-9: 40px;
  --va-space-10: 48px;

  /* Typography */
  --va-font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  --va-font-mono: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace;
  --va-text-xs: 0.65rem;
  --va-text-sm: 0.75rem;
  --va-text-base: 0.85rem;
  --va-text-md: 0.9rem;
  --va-text-lg: 1rem;
  --va-text-xl: 1.15rem;
  --va-text-2xl: clamp(1.25rem, 1.5vw + 1rem, 1.5rem);
  --va-font-regular: 400;
  --va-font-medium: 500;
  --va-font-semibold: 600;
  --va-font-bold: 700;
  --va-letter-spacing-wide: 0.08em;
  --va-letter-spacing-wider: 0.12em;

  /* Layout */
  --va-sidebar-width: 240px;
  --va-sidebar-width-collapsed: 0px;
  --va-topbar-height: 64px;
  --va-actionbar-height: 48px;
  --va-content-max-width: 1200px;
  --va-meta-panel-width: 260px;

  /* Transitions */
  --va-transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
  --va-transition-base: 200ms cubic-bezier(0.4, 0, 0.2, 1);
  --va-transition-slow: 300ms cubic-bezier(0.4, 0, 0.2, 1);
  --va-transition-theme: 200ms cubic-bezier(0.4, 0, 0.2, 1);

  /* Z-index */
  --va-z-sidebar: 1030;
  --va-z-overlay: 1029;
  --va-z-topbar: 1020;
  --va-z-dropdown: 1040;
  --va-z-modal: 1055;
  --va-z-tooltip: 1070;
}
```

## 1.2 Tokens que cambian en modo claro (ver sección 4)

Todos los tokens de superficie, texto, inputs, tablas y segment control tienen contrapartes claras en `theme.css`.

---

# 2. Component Architecture

## Convenciones generales

- Cada bloque BEM se declara **una sola vez** por archivo (Rule #18).
- Los overrides responsive se agrupan en **un único bloque `@media`** al final de cada componente o sección (Rule #17).
- Los estados de JS se manejan con atributos `aria-*` o clases modificadoras BEM (`--active`, `--selected`, `--inactive`, `--error`).
- Cero estilos inline y cero manejadores inline.

---

### `va-body`

**Clase raíz**: `.va-body`

**Estructura HTML**:

```html
<div class="va-body">
  <nav class="va-sidebar" aria-label="Navegació principal">…</nav>
  <div class="va-overlay" data-action="sidebar-close" aria-hidden="true"></div>
  <main class="va-main">…</main>
</div>
```

**Tokens**: `--va-bg-page`, `--va-text`, `--va-font-sans`, `--va-sidebar-width`.

**Comportamiento**: `display: flex; min-height: 100vh;` con `va-main` desplazado `--va-sidebar-width` en desktop.

**Responsive**: en móvil `va-sidebar` es fijo y translateX(-100%); `va-main` tiene margen 0.

**Rule #17 / #18**: `.va-body` y `.va-main` se declaran una vez; sus media queries están agrupadas.

---

### `va-sidebar`

**Clases**: `.va-sidebar`, `.va-sidebar__brand`, `.va-sidebar__logo`, `.va-sidebar__nav`, `.va-sidebar__section`, `.va-sidebar__link`, `.va-sidebar__link--active`, `.va-sidebar__icon`.

**Estructura HTML**:

```twig
<nav class="va-sidebar" id="adminSidebar" aria-label="Navegació principal">
  <a class="va-sidebar__brand" href="{{ path('admin_dashboard') }}">
    <img src="{{ asset('img/logoVora.png') }}" alt="Vora Studio" class="va-sidebar__logo" width="120" height="42">
  </a>
  <div class="va-sidebar__nav">
    <div class="va-sidebar__section">General</div>
    <a class="va-sidebar__link va-sidebar__link--active" href="{{ path('admin_dashboard') }}">
      <i class="bi bi-grid-1x2-fill va-sidebar__icon" aria-hidden="true"></i>
      <span>Dashboard</span>
    </a>
    …
  </div>
</nav>
```

**Tokens**: `--va-sidebar-bg`, `--va-sidebar-border`, `--va-sidebar-text`, `--va-sidebar-hover`, `--va-sidebar-active`, `--va-sidebar-active-accent`, `--va-sidebar-section`, `--va-radius-md`, `--va-transition-base`, `--va-z-sidebar`.

**Modo claro**: `--va-sidebar-bg` pasa a `#f8f8fa`, `--va-sidebar-text` a `#3f3f46`, etc. (ver `theme.css`).

**Responsive**: un único `@media (max-width: 1023px)` oculta la sidebar y define el estado abierto vía `.va-sidebar--open`.

**Rule #17 / #18**: `.va-sidebar` y todos sus elementos se definen una vez; responsive agrupado.

---

### `va-overlay`

**Clase**: `.va-overlay`

**Estructura HTML**: `<div class="va-overlay" data-action="sidebar-close" aria-hidden="true"></div>`

**Tokens**: `--va-z-overlay`, `rgba(0,0,0,0.5)`.

**Comportamiento**: visible solo cuando `.va-overlay--visible` está presente en móvil.

---

### `va-topbar`

**Clases**: `.va-topbar`, `.va-topbar__menu-toggle`, `.va-topbar__title`, `.va-topbar__actions`, `.va-topbar__theme-toggle`, `.va-topbar__theme-icon`.

**Estructura HTML**:

```twig
<header class="va-topbar">
  <div class="va-topbar__start">
    <button class="va-topbar__menu-toggle" data-action="sidebar-toggle" aria-label="Obrir menú">
      <i class="bi bi-list" aria-hidden="true"></i>
    </button>
    <h1 class="va-topbar__title">{% block page_title %}VoraCMS{% endblock %}</h1>
  </div>
  <div class="va-topbar__actions">
    <button class="va-topbar__theme-toggle" data-action="theme-toggle" aria-label="Canviar tema">
      <i class="bi bi-sun-fill va-topbar__theme-icon va-topbar__theme-icon--light" aria-hidden="true"></i>
      <i class="bi bi-moon-fill va-topbar__theme-icon va-topbar__theme-icon--dark" aria-hidden="true"></i>
    </button>
    {% include 'admin/_user_card.html.twig' %}
  </div>
</header>
```

**Tokens**: `--va-topbar-height`, `--va-bg-page`, `--va-border-subtle`, `--va-text`, `--va-z-topbar`, `--va-transition-base`.

**Modo claro**: fondo semitransparente claro con `backdrop-filter`, texto oscuro.

**Responsive**: el título se trunca con `text-overflow: ellipsis` en un único `@media`.

---

### `va-action-bar`

**Clase**: `.va-action-bar`

**Estructura HTML**:

```twig
<div class="va-action-bar">
  {% block topbar_actions %}{% endblock %}
</div>
```

**Tokens**: `--va-actionbar-height`, `--va-border-subtle`, `--va-space-4`, `--va-space-6`.

**Comportamiento**: `display: none` cuando `:empty`.

---

### `va-content`

**Clase**: `.va-content`

**Estructura HTML**:

```twig
<div class="va-content">
  {% block content %}{% endblock %}
</div>
```

**Tokens**: `--va-space-4` a `--va-space-8`, `--va-content-max-width`.

**Responsive**:

```css
.va-content {
  padding: var(--va-space-4);
}

@media (min-width: 768px) {
  .va-content {
    padding: var(--va-space-6);
  }
}

@media (min-width: 1200px) {
  .va-content {
    padding: var(--va-space-8);
  }
}
```

---

### `va-footer`

**Clase**: `.va-footer`

**Estructura HTML**:

```twig
<footer class="va-footer">
  <span>Desenvolupat per</span>
  <img src="{{ asset('img/logoVora.png') }}" alt="Vora Studio" width="80" height="16">
</footer>
```

**Tokens**: `--va-text-muted`, `--va-space-6`, `--va-space-8`.

**Comportamiento**: `margin-top: auto` dentro de `va-main`; no usa posicionamiento fijo.

---

### `va-form` (composición documento + sidebar)

**Clases**: `.va-form`, `.va-form__grid`, `.va-form__document`, `.va-form__meta`, `.va-form__header`, `.va-form__row`, `.va-form__row--cols-2`, `.va-form__field`, `.va-form__label`, `.va-form__required`, `.va-form__help`, `.va-form__actions`.

**Estructura HTML**:

```twig
<form class="va-form" method="post" enctype="multipart/form-data" novalidate>
  <header class="va-form__header">
    <nav class="va-segment" role="tablist" aria-label="Estat de publicació">
      <button type="button" class="va-segment__option va-segment__option--selected" data-value="draft" role="tab" aria-selected="true">…</button>
      <button type="button" class="va-segment__option" data-value="published" role="tab" aria-selected="false">…</button>
      <button type="button" class="va-segment__option" data-value="archived" role="tab" aria-selected="false">…</button>
    </nav>
  </header>

  <div class="va-form__grid">
    <section class="va-form__document">
      <div class="va-form__row va-form__row--cols-2">
        <div class="va-form__field">…</div>
        <div class="va-form__field">…</div>
      </div>
      …
    </section>

    <aside class="va-form__meta">
      <div class="va-status-panel">…</div>
      <div class="va-card">…</div>
      <div class="va-form__actions">…</div>
    </aside>
  </div>
</form>
```

**Tokens**: `--va-bg-card`, `--va-bg-panel`, `--va-border-default`, `--va-radius-xl`, `--va-meta-panel-width`, `--va-topbar-height`, `--va-space-6`.

**Responsive**:

```css
.va-form__grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: var(--va-space-6);
  align-items: start;
}

@media (min-width: 1024px) {
  .va-form__grid {
    grid-template-columns: 1fr var(--va-meta-panel-width);
  }
}
```

```css
.va-form__meta {
  display: flex;
  flex-direction: column;
  gap: var(--va-space-4);
}

@media (min-width: 1024px) {
  .va-form__meta {
    position: sticky;
    top: calc(var(--va-topbar-height) + var(--va-space-6));
  }
}
```

**Rule #17 / #18**: `.va-form__grid` y `.va-form__meta` tienen un único bloque `@media` cada uno.

---

### `va-form__header` + `va-segment`

**Clases**: `.va-form__header`, `.va-segment`, `.va-segment__option`, `.va-segment__option--selected`, `.va-segment__dot`.

**Estructura HTML**:

```twig
<header class="va-form__header">
  <div class="va-form__header-title">
    <h2>Editar entrada</h2>
  </div>
  <div class="va-segment" role="tablist" aria-label="Estat de publicació">
    <button type="button" class="va-segment__option va-segment__option--selected" data-value="draft" role="tab" aria-selected="true">
      <span class="va-segment__dot" aria-hidden="true"></span>
      <span>No publicat</span>
    </button>
    <button type="button" class="va-segment__option" data-value="published" role="tab" aria-selected="false">
      <span class="va-segment__dot va-segment__dot--success" aria-hidden="true"></span>
      <span>Publicat</span>
    </button>
    <button type="button" class="va-segment__option" data-value="archived" role="tab" aria-selected="false">
      <span class="va-segment__dot va-segment__dot--warning" aria-hidden="true"></span>
      <span>Arxivat</span>
    </button>
  </div>
</header>
```

**Tokens**:

- `.va-form__header`: `--va-bg-card`, `--va-border-default`, `--va-radius-xl`, `--va-space-5`, `--va-space-6`.
- `.va-segment`: `--va-segment-bg`, `--va-radius-md`, `--va-space-1`.
- `.va-segment__option`: `--va-segment-text`, `--va-radius-md`, `--va-transition-fast`.
- `.va-segment__option--selected`: `--va-segment-selected`, `--va-segment-text-selected`, `--va-segment-shadow`.

**Modo claro**: `--va-segment-bg: #f0f0f0`, `--va-segment-selected: #ffffff`, `--va-segment-shadow: 0 1px 3px rgba(0,0,0,0.10)`.

**JS**: escucha clicks en `.va-segment__option`, actualiza `aria-selected`, elimina `.va-segment__option--selected` del anterior y sincroniza un `<input type="hidden" name="status">`.

---

### `va-input`

**Clases**: `.va-input`, `.va-input--readonly`, `.va-input--error`, `.va-input--sm`, `.va-form__label`, `.va-form__required`, `.va-form__help`.

**Estructura HTML**:

```twig
<div class="va-form__field">
  <label class="va-form__label" for="field_{{ field.id }}">
    {{ field.name }}
    {% if field.required %}<span class="va-form__required" aria-label="Obligatori">*</span>{% endif %}
  </label>
  <input type="text" id="field_{{ field.id }}" name="field_{{ field.id }}" class="va-input" value="{{ val }}" {% if field.required %}required{% endif %}>
  {% if field.helpText %}<p class="va-form__help">{{ field.helpText }}</p>{% endif %}
</div>
```

**Tokens**: `--va-input-bg`, `--va-input-bg-focus`, `--va-input-border`, `--va-input-border-hover`, `--va-input-border-focus`, `--va-input-color`, `--va-text-placeholder`, `--va-radius-md`, `--va-transition-base`, `--va-danger` (para `--error`).

**Estilos base**:

```css
.va-input {
  width: 100%;
  min-height: 44px;
  padding: 11px 14px;
  background: var(--va-input-bg);
  border: 1px solid var(--va-input-border);
  border-radius: var(--va-radius-md);
  color: var(--va-input-color);
  font-size: var(--va-text-base);
  line-height: 1.5;
  transition:
    border-color var(--va-transition-base),
    background var(--va-transition-base),
    box-shadow var(--va-transition-base);
}

.va-input:hover {
  border-color: var(--va-input-border-hover);
}

.va-input:focus {
  background: var(--va-input-bg-focus);
  border-color: var(--va-input-border-focus);
  outline: none;
}

.va-input::placeholder {
  color: var(--va-text-placeholder);
}

.va-input:disabled,
.va-input--readonly {
  background: var(--va-input-bg-disabled);
  color: var(--va-text-disabled);
  cursor: not-allowed;
}

.va-input--error {
  border-color: var(--va-danger);
}
```

**Modo claro**: `--va-input-bg: #ffffff`, `--va-input-border: #e3e3e3`, `--va-input-bg-focus: #ffffff`, `--va-input-border-focus: #111111`, `--va-text-placeholder: #c5c5c5`.

---

### `va-select`

**Clase**: `.va-select`

**Estructura HTML**:

```twig
<select name="status" class="va-select">
  <option value="draft" …>No publicat</option>
  <option value="published" …>Publicat</option>
  <option value="archived" …>Arxivat</option>
</select>
```

**Tokens**: mismos que `.va-input` más `--va-input-border` para la flecha custom.

**Estilos base**: comparte con `.va-input` mediante selector agrupado `.va-input, .va-select`. Flecha SVG o `appearance: none` con un `background-image` inline **en CSS** (no inline en HTML). La flecha usa `currentColor` para adaptarse al tema.

---

### `va-textarea`

**Clase**: `.va-textarea`

**Estructura HTML**:

```twig
<textarea name="field_{{ field.id }}" class="va-textarea" rows="4" {% if field.required %}required{% endif %}>{{ val }}</textarea>
```

**Tokens**: `--va-input-*` y `--va-textarea-min-height`.

**Estilos base**:

```css
.va-textarea {
  composes: .va-input; /* not real CSS; use grouped selector .va-input, .va-textarea, .va-select */
  min-height: var(--va-textarea-min-height, 120px);
  resize: vertical;
  line-height: 1.75;
}
```

---

### `va-checkbox` / `va-radio`

**Clases**: `.va-check`, `.va-check__input`, `.va-check__control`, `.va-check__label`, `.va-check--radio`.

**Estructura HTML**:

```twig
<label class="va-check">
  <input type="checkbox" name="field_{{ field.id }}" class="va-check__input" value="1" {% if val %}checked{% endif %}>
  <span class="va-check__control" aria-hidden="true"></span>
  <span class="va-check__label">Sí</span>
</label>
```

**Tokens**: `--va-input-bg`, `--va-input-border`, `--va-primary`, `--va-radius-sm`, `--va-transition-base`.

**Estilos base**:

```css
.va-check {
  display: inline-flex;
  align-items: center;
  gap: var(--va-space-2);
  cursor: pointer;
}

.va-check__input {
  position: absolute;
  opacity: 0;
  width: 1px;
  height: 1px;
}

.va-check__control {
  width: 18px;
  height: 18px;
  flex-shrink: 0;
  background: var(--va-input-bg);
  border: 1px solid var(--va-input-border);
  border-radius: var(--va-radius-sm);
  transition: background var(--va-transition-base), border-color var(--va-transition-base);
}

.va-check__input:checked + .va-check__control {
  background: var(--va-primary);
  border-color: var(--va-primary);
}

.va-check__input:focus-visible + .va-check__control {
  box-shadow: 0 0 0 3px var(--va-primary-bg);
}

.va-check--radio .va-check__control {
  border-radius: var(--va-radius-pill);
}
```

---

### `va-upload`

**Clases**: `.va-upload`, `.va-upload__icon`, `.va-upload__body`, `.va-upload__title`, `.va-upload__hint`, `.va-upload__input`.

**Estructura HTML**:

```twig
<div class="va-upload">
  <input type="file" id="file_{{ fieldId }}" name="field_{{ fieldId }}_files[]" class="va-upload__input" accept=".jpg,.jpeg,.webp,.avif" multiple>
  <label for="file_{{ fieldId }}" class="va-upload__target">
    <span class="va-upload__icon"><i class="bi bi-cloud-upload" aria-hidden="true"></i></span>
    <span class="va-upload__body">
      <span class="va-upload__title">Seleccionar fitxers</span>
      <span class="va-upload__hint">JPG, WebP o AVIF · Màxim 1MB cadascuna</span>
    </span>
  </label>
</div>
```

**Tokens**: `--va-border-strong`, `--va-border-default`, `--va-input-bg`, `--va-text-muted`, `--va-text-secondary`, `--va-radius-md`, `--va-transition-base`.

**Estilos base**:

```css
.va-upload__target {
  display: flex;
  align-items: center;
  gap: var(--va-space-3);
  padding: var(--va-space-4);
  border: 1.5px dashed var(--va-border-strong);
  border-radius: var(--va-radius-md);
  background: var(--va-input-bg);
  cursor: pointer;
  transition: border-color var(--va-transition-base), background var(--va-transition-base);
}

.va-upload__target:hover {
  border-color: var(--va-input-border-focus);
  background: var(--va-input-bg-focus);
}

.va-upload__input {
  position: absolute;
  opacity: 0;
  width: 1px;
  height: 1px;
}
```

---

### `va-gallery`

**Clases**: `.va-gallery`, `.va-gallery__grid`, `.va-gallery__thumb`, `.va-gallery__image`, `.va-gallery__remove`, `.va-gallery__add`, `.va-gallery__input`.

**Estructura HTML**:

```twig
<div class="va-gallery" data-role="gallery" data-field-id="{{ fieldId }}">
  <div class="va-gallery__tools">
    <div class="va-upload va-upload--compact">…</div>
    <button type="button" class="va-btn va-btn--ghost" data-role="media-picker" data-field="{{ fieldId }}" data-multiple="true" aria-label="Seleccionar de la mediateca">
      <i class="bi bi-images" aria-hidden="true"></i>
    </button>
  </div>
  <input type="hidden" name="field_{{ fieldId }}" class="va-gallery__value" value="{{ value }}">
  <div class="va-gallery__grid">
    {% for gid in itemIds %}
    <figure class="va-gallery__thumb">
      <img src="{{ mediaPaths[gid] ?? '' }}" alt="" class="va-gallery__image" width="120" height="120">
      <button type="button" class="va-gallery__remove" aria-label="Eliminar imatge" data-remove="{{ gid }}">
        <i class="bi bi-x" aria-hidden="true"></i>
      </button>
    </figure>
    {% endfor %}
    <label class="va-gallery__add va-upload__target" for="file_{{ fieldId }}_g">…</label>
  </div>
</div>
```

**Tokens**: `--va-border-default`, `--va-radius-md`, `--va-space-2`, `--va-space-3`, `--va-danger`, `--va-shadow-md`.

**Responsive grid**:

```css
.va-gallery__grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: var(--va-space-3);
}

@media (min-width: 480px) {
  .va-gallery__grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (min-width: 768px) {
  .va-gallery__grid {
    grid-template-columns: repeat(4, 1fr);
  }
}
```

**Rule #17**: `.va-gallery__grid` tiene un único bloque `@media`.

---

### `va-status-panel` / `va-status-option`

**Clases**: `.va-status-panel`, `.va-status-panel__title`, `.va-status-option`, `.va-status-option--selected`, `.va-status-option__dot`, `.va-status-option__name`, `.va-status-option__check`.

**Estructura HTML**:

```twig
<fieldset class="va-status-panel">
  <legend class="va-status-panel__title">Estat</legend>
  <div class="va-status-panel__options">
    <label class="va-status-option {% if entry.status == 'draft' %}va-status-option--selected{% endif %}">
      <input type="radio" name="status" value="draft" class="va-status-option__input" {% if entry.status == 'draft' %}checked{% endif %}>
      <span class="va-status-option__dot va-status-option__dot--draft" aria-hidden="true"></span>
      <span class="va-status-option__name">No publicat</span>
      <i class="bi bi-check-lg va-status-option__check" aria-hidden="true"></i>
    </label>
    <label class="va-status-option {% if entry.status == 'published' %}va-status-option--selected{% endif %}">
      <input type="radio" name="status" value="published" class="va-status-option__input" {% if entry.status == 'published' %}checked{% endif %}>
      <span class="va-status-option__dot va-status-option__dot--published" aria-hidden="true"></span>
      <span class="va-status-option__name">Publicat</span>
      <i class="bi bi-check-lg va-status-option__check" aria-hidden="true"></i>
    </label>
    <label class="va-status-option {% if entry.status == 'archived' %}va-status-option--selected{% endif %}">
      <input type="radio" name="status" value="archived" class="va-status-option__input" {% if entry.status == 'archived' %}checked{% endif %}>
      <span class="va-status-option__dot va-status-option__dot--archived" aria-hidden="true"></span>
      <span class="va-status-option__name">Arxivat</span>
      <i class="bi bi-check-lg va-status-option__check" aria-hidden="true"></i>
    </label>
  </div>
</fieldset>
```

**Tokens**: `--va-bg-panel`, `--va-border-default`, `--va-radius-xl`, `--va-text-muted`, `--va-status-draft`, `--va-status-published`, `--va-status-archived`, `--va-sidebar-hover`, `--va-sidebar-active`, `--va-transition-fast`.

**Estilos base**:

```css
.va-status-panel {
  background: var(--va-bg-panel);
  border: 1px solid var(--va-border-default);
  border-radius: var(--va-radius-xl);
  padding: var(--va-space-4);
}

.va-status-panel__title {
  font-size: var(--va-text-xs);
  font-weight: var(--va-font-bold);
  letter-spacing: var(--va-letter-spacing-wider);
  text-transform: uppercase;
  color: var(--va-text-muted);
  margin-bottom: var(--va-space-3);
}

.va-status-option {
  display: flex;
  align-items: center;
  gap: var(--va-space-2);
  padding: var(--va-space-2) var(--va-space-3);
  border-radius: var(--va-radius-md);
  border: 1px solid transparent;
  cursor: pointer;
  transition: background var(--va-transition-fast), border-color var(--va-transition-fast);
}

.va-status-option:hover {
  background: var(--va-sidebar-hover);
}

.va-status-option--selected {
  background: rgba(255, 255, 255, 0.08);
  border-color: var(--va-border-strong);
}

.va-status-option__dot {
  width: 8px;
  height: 8px;
  border-radius: var(--va-radius-pill);
  flex-shrink: 0;
}

.va-status-option__dot--draft { background: var(--va-status-draft); }
.va-status-option__dot--published { background: var(--va-status-published); }
.va-status-option__dot--archived { background: var(--va-status-archived); }

.va-status-option__check {
  margin-left: auto;
  opacity: 0;
  color: var(--va-text-secondary);
}

.va-status-option--selected .va-status-option__check {
  opacity: 1;
}
```

**Modo claro**: `--va-bg-panel: #ffffff`, `--va-status-option--selected: #f0f0f0`, bordes oscuros.

---

### `va-table`

**Clases**: `.va-table-wrapper`, `.va-table`, `.va-table__header`, `.va-table__body`, `.va-table__row`, `.va-table__row--inactive`, `.va-table__cell`, `.va-table__cell--actions`, `.va-table__actions`, `.va-table__sort`.

**Estructura HTML**:

```twig
<div class="va-table-wrapper">
  <table class="va-table">
    <thead class="va-table__header">
      <tr>
        <th class="va-table__cell">Títol</th>
        <th class="va-table__cell">Estat</th>
        <th class="va-table__cell va-table__cell--actions" aria-label="Accions"></th>
      </tr>
    </thead>
    <tbody class="va-table__body">
      {% for e in entries %}
      <tr class="va-table__row {% if e.status == 'archived' %}va-table__row--inactive{% endif %}">
        <td class="va-table__cell">{{ e.title }}</td>
        <td class="va-table__cell">{% include 'admin/components/_badge.html.twig' with {status: e.status} %}</td>
        <td class="va-table__cell va-table__cell--actions">
          <div class="va-table__actions">
            <a href="{{ path('admin_entry_edit', {id: e.id}) }}" class="va-btn va-btn--ghost va-btn--icon" aria-label="Editar">
              <i class="bi bi-pencil" aria-hidden="true"></i>
            </a>
            <button type="button" class="va-btn va-btn--ghost va-btn--icon va-btn--danger" data-action="delete-entry" data-id="{{ e.id }}" aria-label="Eliminar">
              <i class="bi bi-trash" aria-hidden="true"></i>
            </button>
          </div>
        </td>
      </tr>
      {% endfor %}
    </tbody>
  </table>
</div>
```

**Tokens**: `--va-bg-table-header`, `--va-bg-row-hover`, `--va-bg-row-inactive`, `--va-border-subtle`, `--va-text`, `--va-text-secondary`, `--va-radius-md`, `--va-space-3`, `--va-space-4`.

**Responsive**:

```css
.va-table-wrapper {
  width: 100%;
  overflow-x: auto;
  border: 1px solid var(--va-border-subtle);
  border-radius: var(--va-radius-lg);
}

.va-table {
  width: 100%;
  min-width: 640px;
  border-collapse: collapse;
}
```

---

### `va-badge`

**Clases**: `.va-badge`, `.va-badge--success`, `.va-badge--warning`, `.va-badge--danger`, `.va-badge--neutral`, `.va-badge--info`.

**Estructura HTML**:

```twig
<span class="va-badge va-badge--{{ status|default('neutral') }}">
  {{ label }}
</span>
```

**Tokens**: `--va-success`, `--va-success-bg`, `--va-warning`, `--va-warning-bg`, `--va-danger`, `--va-danger-bg`, `--va-radius-pill`, `--va-text-xs`, `--va-font-semibold`.

**Estilos base**:

```css
.va-badge {
  display: inline-flex;
  align-items: center;
  gap: var(--va-space-1);
  padding: var(--va-space-1) var(--va-space-2);
  border-radius: var(--va-radius-pill);
  font-size: var(--va-text-xs);
  font-weight: var(--va-font-semibold);
  line-height: 1;
}

.va-badge--success {
  background: var(--va-success-bg);
  color: var(--va-success);
}

.va-badge--warning {
  background: var(--va-warning-bg);
  color: var(--va-warning);
}

.va-badge--danger {
  background: var(--va-danger-bg);
  color: var(--va-danger);
}

.va-badge--neutral {
  background: rgba(255, 255, 255, 0.08);
  color: var(--va-text-muted);
}
```

**Modo claro**: `--va-success-bg: #e8f5e9`, `--va-warning-bg: #fff3e0`, `--va-danger-bg: #ffebee`, `--va-badge-neutral-bg: #f0f0f5`, `--va-badge-neutral-text: #6b6b80`.

---

### `va-btn`

**Clases**: `.va-btn`, `.va-btn--primary`, `.va-btn--secondary`, `.va-btn--ghost`, `.va-btn--danger`, `.va-btn--icon`, `.va-btn--sm`, `.va-btn--lg`.

**Estructura HTML**:

```twig
<button type="submit" class="va-btn va-btn--primary va-btn--lg">
  <i class="bi bi-check-lg" aria-hidden="true"></i>
  <span>Guardar canvis</span>
</button>

<a href="{{ path('admin_entry_by_type', {slug: contentType.slug}) }}" class="va-btn va-btn--ghost">
  <i class="bi bi-arrow-left" aria-hidden="true"></i>
  <span>Tornar</span>
</a>
```

**Tokens**: `--va-primary`, `--va-primary-hover`, `--va-primary-light`, `--va-text`, `--va-text-secondary`, `--va-danger`, `--va-border-strong`, `--va-radius-md`, `--va-transition-base`, `--va-shadow-sm`.

**Estilos base**:

```css
.va-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: var(--va-space-2);
  min-height: 44px;
  padding: 11px 22px;
  border: 1px solid transparent;
  border-radius: var(--va-radius-md);
  font-size: var(--va-text-sm);
  font-weight: var(--va-font-semibold);
  line-height: 1;
  text-decoration: none;
  cursor: pointer;
  transition: background var(--va-transition-base), border-color var(--va-transition-base), transform var(--va-transition-base), box-shadow var(--va-transition-base);
}

.va-btn--primary {
  background: var(--va-primary);
  color: #fff;
}

.va-btn--primary:hover {
  background: var(--va-primary-hover);
}

.va-btn--secondary {
  background: var(--va-input-bg);
  border-color: var(--va-border-strong);
  color: var(--va-text);
}

.va-btn--secondary:hover {
  background: var(--va-input-bg-focus);
}

.va-btn--ghost {
  background: transparent;
  color: var(--va-text-secondary);
}

.va-btn--ghost:hover {
  background: var(--va-input-bg);
  color: var(--va-text);
}

.va-btn--danger {
  color: var(--va-danger);
}

.va-btn--danger:hover {
  background: var(--va-danger-bg);
}

.va-btn--icon {
  padding: var(--va-space-2);
  min-height: 34px;
  width: 34px;
}

.va-btn--sm {
  min-height: 34px;
  padding: 7px 14px;
  font-size: var(--va-text-xs);
}

.va-btn--lg {
  min-height: 52px;
  padding: 14px 32px;
}
```

---

### `va-card`

**Clases**: `.va-card`, `.va-card__header`, `.va-card__title`, `.va-card__body`, `.va-card__footer`, `.va-card--flush`.

**Estructura HTML**:

```twig
<article class="va-card">
  <header class="va-card__header">
    <h3 class="va-card__title">Últims clients</h3>
  </header>
  <div class="va-card__body">…</div>
</article>
```

**Tokens**: `--va-bg-card`, `--va-border-default`, `--va-radius-xl`, `--va-shadow-lg`, `--va-space-5`, `--va-space-6`.

**Modo claro**: `--va-bg-card: #ffffff`, `--va-border-default: rgba(0,0,0,0.08)`, `--va-shadow-lg: 0 8px 32px rgba(0,0,0,0.08)`.

---

### `va-stat-card`

**Clases**: `.va-stat-card`, `.va-stat-card__icon`, `.va-stat-card__value`, `.va-stat-card__label`, `.va-stat-card--success`, `.va-stat-card--accent`.

**Estructura HTML**:

```twig
<div class="va-stat-card">
  <div class="va-stat-card__icon"><i class="bi bi-file-text" aria-hidden="true"></i></div>
  <div class="va-stat-card__value">{{ metrics.totalEntries }}</div>
  <div class="va-stat-card__label">Entrades</div>
</div>
```

**Tokens**: `--va-bg-card`, `--va-border-default`, `--va-radius-xl`, `--va-text`, `--va-text-secondary`, `--va-success`, `--va-info`, `--va-space-4`.

---

### `va-metrics-grid`

**Clase**: `.va-metrics-grid`

**Estructura HTML**:

```twig
<div class="va-metrics-grid">
  <div class="va-stat-card">…</div>
  <div class="va-stat-card">…</div>
  <div class="va-stat-card">…</div>
  <div class="va-stat-card">…</div>
</div>
```

**Responsive**:

```css
.va-metrics-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: var(--va-space-4);
}

@media (min-width: 480px) {
  .va-metrics-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1024px) {
  .va-metrics-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}
```

---

### `va-login`

**Clases**: `.va-login`, `.va-login__card`, `.va-login__brand`, `.va-login__logo`, `.va-login__error`, `.va-login__footer`.

**Estructura HTML**:

```twig
<div class="va-login">
  <div class="va-login__card">
    <div class="va-login__brand">
      <img src="{{ asset('img/logoVora.png') }}" alt="Vora Studio" class="va-login__logo" width="140" height="48">
      <p>Inicia sessió per continuar</p>
    </div>
    {% if error %}
      <div class="va-alert va-alert--error" role="alert">
        <i class="bi bi-x-lg" aria-hidden="true"></i>
        <span>{{ error.messageKey|trans(error.messageData, 'security') }}</span>
      </div>
    {% endif %}
    <form method="post" class="va-form va-form--login">
      <div class="va-form__field">
        <label class="va-form__label" for="email">Email</label>
        <input type="email" id="email" name="_username" class="va-input" value="{{ last_email }}" required autofocus>
      </div>
      <div class="va-form__field">
        <label class="va-form__label" for="password">Contrasenya</label>
        <input type="password" id="password" name="_password" class="va-input" required>
      </div>
      <button type="submit" class="va-btn va-btn--primary va-btn--lg va-btn--block">Entrar</button>
    </form>
  </div>
  <footer class="va-login__footer">…</footer>
</div>
```

**Tokens**: `--va-bg-page`, `--va-bg-card`, `--va-border-default`, `--va-radius-2xl`, `--va-shadow-xl`, `--va-space-6`, `--va-space-8`.

**CSS cargado en login**: `admin/root.css`, `admin/forms.css`, `admin/components.css`, `admin/login.css`, `admin/theme.css`.

---

### `va-alert`

**Clases**: `.va-alert`, `.va-alert--error`, `.va-alert--success`, `.va-alert--warning`, `.va-alert--info`.

**Estructura HTML**:

```twig
<div class="va-alert va-alert--error" role="alert">
  <i class="bi bi-x-lg va-alert__icon" aria-hidden="true"></i>
  <span class="va-alert__message">Credencials incorrectes</span>
</div>
```

**Tokens**: `--va-danger`, `--va-danger-bg`, `--va-success`, `--va-success-bg`, `--va-warning`, `--va-warning-bg`, `--va-info`, `--va-info-bg`, `--va-radius-lg`, `--va-space-3`, `--va-space-4`.

---

### `va-modal`

**Clases**: `.va-modal`, `.va-modal__overlay`, `.va-modal__dialog`, `.va-modal__header`, `.va-modal__title`, `.va-modal__body`, `.va-modal__footer`, `.va-modal--open`.

**Estructura HTML**:

```twig
<div class="va-modal" id="mediaPickerModal" role="dialog" aria-modal="true" aria-labelledby="mediaPickerTitle">
  <div class="va-modal__overlay" data-action="modal-close"></div>
  <div class="va-modal__dialog">
    <header class="va-modal__header">
      <h2 class="va-modal__title" id="mediaPickerTitle">Seleccionar imatge</h2>
      <button type="button" class="va-btn va-btn--ghost va-btn--icon" data-action="modal-close" aria-label="Tancar">
        <i class="bi bi-x-lg" aria-hidden="true"></i>
      </button>
    </header>
    <div class="va-modal__body">…</div>
    <footer class="va-modal__footer">…</footer>
  </div>
</div>
```

**Tokens**: `--va-z-modal`, `--va-bg-card`, `--va-border-default`, `--va-radius-2xl`, `--va-shadow-xl`, `--va-text`, `--va-text-secondary`.

---

### `va-user-card`

**Clases**: `.va-user-card`, `.va-user-card__avatar`, `.va-user-card__info`, `.va-user-card__name`, `.va-user-card__role`, `.va-user-card__menu`.

**Estructura HTML**:

```twig
<div class="va-user-card">
  <img src="{{ asset('img/default-avatar.png') }}" alt="" class="va-user-card__avatar" width="30" height="30">
  <div class="va-user-card__info">
    <span class="va-user-card__name">{{ app.user.name }}</span>
    <span class="va-user-card__role">{{ app.user.roles[0]|replace({'ROLE_': ''})|lower }}</span>
  </div>
  <a href="{{ path('admin_logout') }}" class="va-user-card__logout" aria-label="Tancar sessió">
    <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
  </a>
</div>
```

**Tokens**: `--va-input-bg`, `--va-border-strong`, `--va-text`, `--va-text-secondary`, `--va-radius-md`, `--va-transition-base`.

---

# 3. File Structure & Inheritance

## 3.1 Orden de carga en `layout.html.twig`

```twig
{% block stylesheets %}
    {{ parent() }}
    <link rel="stylesheet" href="{{ asset('css/admin/root.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/tables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/theme.css') }}">
{% endblock %}
```

`login.html.twig` no extiende `layout` y carga solo lo necesario:

```twig
{% block stylesheets %}
    {{ parent() }}
    <link rel="stylesheet" href="{{ asset('css/admin/root.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/theme.css') }}">
{% endblock %}
```

## 3.2 Estrategia de media queries

Cada archivo CSS tiene la siguiente estructura:

1. Sección de componentes con sus estilos base.
2. Un único bloque de responsive al final del archivo (o del componente) que agrupa TODOS los `@media` necesarios.

Ejemplo en `layout.css`:

```css
/* === SIDEBAR === */
.va-sidebar { … }
.va-sidebar__link { … }

/* === TOPBAR === */
.va-topbar { … }

/* === RESPONSIVE === */
@media (max-width: 1023px) {
  .va-sidebar { … }
  .va-sidebar--open { … }
  .va-overlay--visible { … }
  .va-main { margin-left: 0; }
  .va-topbar__menu-toggle { display: flex; }
}

@media (min-width: 1024px) {
  .va-topbar__menu-toggle { display: none; }
}
```

No se permiten bloques `@media` dispersos para la misma clase.

---

# 4. Theme Strategy (`public/css/admin/theme.css`)

```css
[data-theme="light"] {
  /* Page surfaces */
  --va-bg-page: #f5f5f7;
  --va-bg-page-gradient-start: #f5f5f7;
  --va-bg-page-gradient-mid: #ffffff;
  --va-bg-page-gradient-end: #f0f0f2;
  --va-bg-card: #ffffff;
  --va-bg-card-hover: #f8f8fa;
  --va-bg-panel: #ffffff;
  --va-bg-elevated: #ffffff;

  /* Sidebar */
  --va-sidebar-bg: #f8f8fa;
  --va-sidebar-border: rgba(0, 0, 0, 0.07);
  --va-sidebar-text: #3f3f46;
  --va-sidebar-text-hover: #111111;
  --va-sidebar-hover: rgba(0, 0, 0, 0.04);
  --va-sidebar-active: rgba(73, 69, 255, 0.10);
  --va-sidebar-section: #71717a;

  /* Text */
  --va-text: #111111;
  --va-text-secondary: #555555;
  --va-text-muted: #888888;
  --va-text-placeholder: #c5c5c5;
  --va-text-disabled: #a1a1aa;

  /* Inputs */
  --va-input-bg: #ffffff;
  --va-input-bg-focus: #ffffff;
  --va-input-bg-disabled: #f4f4f5;
  --va-input-border: #e3e3e3;
  --va-input-border-hover: #c5c5c5;
  --va-input-border-focus: #111111;
  --va-input-color: #111111;

  /* Segment */
  --va-segment-bg: #f0f0f0;
  --va-segment-selected: #ffffff;
  --va-segment-text: #888888;
  --va-segment-text-selected: #111111;
  --va-segment-shadow: 0 1px 3px rgba(0, 0, 0, 0.10);

  /* Borders */
  --va-border-subtle: rgba(0, 0, 0, 0.06);
  --va-border-default: rgba(0, 0, 0, 0.08);
  --va-border-strong: rgba(0, 0, 0, 0.12);

  /* Shadows */
  --va-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.06);
  --va-shadow-md: 0 4px 16px rgba(0, 0, 0, 0.08);
  --va-shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.10);
  --va-shadow-xl: 0 24px 64px rgba(0, 0, 0, 0.12);

  /* Status (colores semánticos mantienos la saturación; cambian fondos) */
  --va-success-bg: #e8f5e9;
  --va-warning-bg: #fff3e0;
  --va-danger-bg: #ffebee;
  --va-info-bg: #e0f7fa;
}
```

**Regla crítica**: `theme.css` contiene **únicamente** el selector `[data-theme="light"]` y reasignaciones de variables. No define `.va-*` ni modifica propiedades directamente.

---

# 5. Migration Plan

## 5.1 PR-1 — Foundation, Layout & Login

**CSS**:
- Crear `public/css/admin/root.css` con todos los tokens.
- Crear `public/css/admin/layout.css`.
- Crear `public/css/admin/components.css` con botones, badges, alerts, cards, modals, segment, user-card.
- Crear `public/css/admin/forms.css` con inputs, selects, textareas, checkbox, radio, upload.
- Crear `public/css/admin/login.css`.
- Crear `public/css/admin/theme.css`.

**Templates**:
- `templates/admin/layout.html.twig`
- `templates/admin/login.html.twig`
- `templates/admin/_user_card.html.twig`

**JS**:
- Añadir theme toggle y persistencia en `localStorage`.
- Añadir sidebar toggle y overlay click.

**Verificación**:
- Layout y login renderizan correctamente en ambos temas.
- Sidebar funciona en 375px.

## 5.2 PR-2 — Forms, Tables & Content

**Templates**:
- `templates/admin/entry/{new,edit,show,index}.html.twig`
- `templates/admin/content-type/{new,edit,index}.html.twig`
- `templates/admin/base-content/{new,edit,show,index}.html.twig`
- `templates/admin/project/{form,index,show}.html.twig`
- `templates/admin/user/{form,index}.html.twig`
- `templates/admin/media/{index,picker}.html.twig`

**Components**:
- `templates/admin/components/_gallery_field.html.twig`
- `templates/admin/components/_repeater_field.html.twig`
- `templates/admin/components/_quill_field.html.twig`
- `templates/admin/components/_badge.html.twig`
- `templates/admin/components/_status.html.twig`
- `templates/admin/components/_toggle_btn.html.twig`
- `templates/admin/components/_section_header.html.twig`

**CSS**:
- Crear `public/css/admin/tables.css`.
- Ajustar `forms.css` y `components.css` según casos reales descubiertos.

**JS**:
- Extraer `onclick="toggleFieldOptions(this)"` a listeners delegados.
- Extraer handlers de galería y media picker a `admin.js`.

## 5.3 PR-3 — Dashboard, Components & Cleanup

**Templates**:
- `templates/admin/dashboard.html.twig`
- `templates/admin/components/_dashboard_stat_card.html.twig`
- `templates/admin/components/_stat_mini.html.twig`
- `templates/admin/api-guide.html.twig`

**CSS**:
- Crear `public/css/admin/dashboard.css`.
- Revisar y limpiar tokens no usados.

**Cleanup**:
- Eliminar `public/css/admin.css`.
- Actualizar cualquier referencia residual en `base.html.twig`.

**Verificación final**:

```bash
grep -R "class=\"[^\"]*s-\|class=\"[^\"]*cyber-" templates/admin/
grep -R "style=\|onclick=\|onchange=\|onmouseover=\|onmouseout=" templates/admin/
test -f public/css/admin.css && echo "FAIL: admin.css aun existe" || echo "OK"
ls public/css/admin/
```

---

## Open Questions

- [ ] Should the existing `public/css/root.css` keep `--s-*` tokens for legacy public pages, or will they be migrated separately? This design assumes they remain untouched.
- [ ] Is Bootstrap 5 used in public pages only, or also in admin? Current admin templates rely on Bootstrap grid; this design keeps Bootstrap but overrides all visual classes with `va-*`.
- [ ] Are there any admin pages with custom inline JS not captured in the sampled templates (e.g., Quill init, preview modals) that need extraction to `admin.js`? A full template scan during PR-2 will confirm.
