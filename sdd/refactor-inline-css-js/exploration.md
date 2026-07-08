## Exploration: Refactor inline CSS/JS to external files

### Current State

The project has a significant amount of inline CSS and JS across 17 production files spanning 4 sites (VoraStudio, voracms, voraRaymel, aulaGastronomica). No site links `reset.css` from the shared `css/` directory — only `aulaGastronomica/index.html` has its own local `reset.css`. None of the sites link the shared `root.css` tokens file directly.

### Files Audit Table

| File | Inline `<style>` | Inline `style=""` | Inline `<script>` | Inline events | reset.css? | root.css? |
|---|---|---|---|---|---|---|
| **VoraStudio** | | | | | | |
| VoraStudio/index.php | 0 | 2 (honeypot div, CTA a) | 1 (gtag) | 0 | No | No |
| VoraStudio/html/projectes.html | 0 | 1 (btn-cta border) | 2 (gtag, Lenis init) | 0 | No | No |
| VoraStudio/html/serveis.php | 0 | 2 (honeypot div, btn-cta border) | 1 (gtag) | 1 (`javascript:void(0)` on a) | No | No |
| VoraStudio/contacte.php | 0 | 0 | 0 | 0 | — | — |
| VoraStudio/projectes/projecte.php | 0 | 2 (honeypot div, btn-cta border) | 0 | 0 | No | No |
| **voracms** | | | | | | |
| base.html.twig | 0 | 0 | 2 (gtag, form validation ~60 lines) | 0 | No (Bootstrap) | No |
| admin/layout.html.twig | 0 | 0 | 2 (swalConfirm, ~45 lines + flash/theme ~60 lines) | 0 | No (admin.css) | No |
| admin/_user_card.html.twig | 0 | 0 | 0 | 0 | No | No |
| admin/dashboard.html.twig | 0 | many (metric cards, list items, hover handlers) | 0 | 6 (`onmouseover`, `onmouseout` on links) | No | No |
| admin/content-type/edit.html.twig | 0 | 0 | 1 (field management ~15 lines) | 0 | No | No |
| admin/content-type/index.html.twig | 0 | 0 | 0 | 1 (`onclick` swalConfirm) | No | No |
| admin/content-type/new.html.twig | 0 | 0 | 1 (field management + auto-slug ~20 lines) | 0 | No | No |
| admin/entry/edit.html.twig | 0 | many (gallery items, quill wrappers) | 2 (Quill init + gallery/media ~135 lines) | 0 | No | No |
| admin/entry/index.html.twig | 0 | many (table cells, action buttons) | 1 (toggle AJAX ~30 lines) | 1 (`onclick` swalConfirm) | No | No |
| admin/entry/new.html.twig | 0 | many (gallery items, quill wrappers) | 2 (Quill init + gallery/media ~140 lines) | 0 | No | No |
| admin/entry/show.html.twig | 0 | many (cards, grids, images) | 0 | 0 | No | No |
| admin/media/index.html.twig | 0 | 0 | 2 (upload AJAX + copy URL ~45 lines) | 1 (`onclick` swalConfirm) | No | No |
| admin/media/picker.html.twig | 1 (picker styles ~14 lines) | 0 | 2 (selection + upload ~100 lines) | 0 | No | No |
| admin/project/form.html.twig | 0 | 0 | 1 (color picker ~10 lines) | 0 | No | No |
| admin/project/index.html.twig | 0 | many (cards, rows, status badges) | 0 | 2 (`onclick` on cards + swalConfirm) | No | No |
| admin/project/show.html.twig | 0 | many (section header, stat cards) | 0 | 0 | No | No |
| admin/user/form.html.twig | 0 | 0 | 0 | 0 | No | No |
| admin/user/index.html.twig | 0 | many (cells, avatar, badges) | 1 (toggle AJAX ~30 lines) | 1 (`onclick` swalConfirm) | No | No |
| **voraRaymel** | | | | | | |
| voraRaymel/index.php | 0 | 0 | 1 (RECAPTCHA_SITE_KEY) | 1 (`onclick` lang-switcher) | No | No |
| voraRaymel/productes.html | 0 | 0 | 0 | 9 (`onclick` on lang-switcher + mobile menu links) | No | No |
| voraRaymel/privacitat.html | 1 (~94 lines legal-page styles) | 1 (footer padding) | 0 | 0 | No | No |
| voraRaymel/legal.html | 1 (~81 lines legal-page styles) | 1 (footer padding) | 0 | 0 | No | No |
| voraRaymel/cookies.html | 1 (~149 lines legal-page styles) | 1 (footer padding) | 0 | 0 | No | No |
| **aulaGastronomica** | | | | | | |
| aulaGastronomica/index.html | 0 | 5 (`display:block` on 3 sections, 2 banner background-images) | 1 (js-enabled class) | 0 | Yes (local) | No |

### Totals Summary

- **Inline `<style>` blocks**: 3 files (voraRaymel privacitat/legal/cookies + media/picker.html.twig)
- **Inline `style=""` attributes**: Found in 12 files (mostly honeypot divs, CTA buttons, footer padding, admin template cards/galleries)
- **Inline `<script>` blocks with JS code**: Found in 14 files — gtag is duplicated 5 times across VoraStudio pages; admin templates have large blocks for Quill initialization, gallery management, toggle AJAX, and form validation
- **Inline event handlers (`onclick`, `onmouseover`, `onmouseout`)**: Found in 7 files (voraRaymel has the most with 9 `onclick` on mobile menu + lang-switcher, admin templates use `onmouseover`/`onmouseout` on dashboard list items, `onclick` for delete confirmations)
- **Missing `reset.css`**: 16 out of 17 pages (except aulaGastronomica)
- **Missing `root.css`**: ALL pages

### Affected Areas

- `VoraStudio/index.php` — inline gtag, honeypot `style`, CTA inline style
- `VoraStudio/html/projectes.html` — inline gtag, Lenis init `<script>`, CTA inline style
- `VoraStudio/html/serveis.php` — inline gtag, honeypot `style`, `javascript:void(0)`, CTA inline style
- `VoraStudio/projectes/projecte.php` — honeypot `style`, CTA inline style
- `voracms/templates/base.html.twig` — inline gtag + 60-line form validation `<script>` (shared by all admin pages)
- `voracms/templates/admin/layout.html.twig` — swalConfirm function + flash/theme toggle script (shared by all admin pages)
- `voracms/templates/admin/dashboard.html.twig` — 6 `onmouseover`/`onmouseout` handlers, dozens of `style` attributes on metric cards
- `voracms/templates/admin/entry/edit.html.twig` — 135 lines of inline JS (Quill, gallery, media picker)
- `voracms/templates/admin/entry/new.html.twig` — 140 lines of inline JS (same patterns)
- `voracms/templates/admin/content-type/new.html.twig` — auto-slug JS
- `voracms/templates/admin/media/index.html.twig` — 45 lines inline JS (upload AJAX, copy URL)
- `voracms/templates/admin/media/picker.html.twig` — 14-line `<style>` + 100 lines inline JS
- `voracms/templates/admin/user/index.html.twig` — toggle AJAX inline JS
- `voracms/templates/admin/entry/index.html.twig` — toggle AJAX inline JS
- `voracms/templates/admin/project/index.html.twig` — `onclick` card navigation + swalConfirm
- `voracms/templates/admin/project/form.html.twig` — color picker inline JS
- `voracms/templates/admin/content-type/edit.html.twig` — field management inline JS
- `voracms/templates/admin/content-type/new.html.twig` — field management + auto-slug inline JS
- `voracms/templates/admin/user/form.html.twig` — clean (no inline)
- `voracms/templates/admin/_user_card.html.twig` — clean
- `voracms/templates/admin/entry/show.html.twig` — many `style` attributes but no inline JS
- `voracms/templates/admin/project/show.html.twig` — some `style` attributes but no inline JS
- `voraRaymel/index.php` — inline JS (RECAPTCHA_SITE_KEY), `onclick` lang-switcher
- `voraRaymel/productes.html` — 9 `onclick` on mobile menu + lang-switcher
- `voraRaymel/privacitat.html` — 94-line `<style>` block (legal page styles), inline footer `style`
- `voraRaymel/legal.html` — 81-line `<style>` block (legal page styles), inline footer `style`
- `voraRaymel/cookies.html` — 149-line `<style>` block (legal page styles), inline footer `style`
- `aulaGastronomica/index.html` — inline `<script>` (js-enabled), `style="display:block"` on 3 sections, `style="background-image"` on 3 banners

### Approaches

1. **Approach A — Per-site consolidated extraction** (Recommended)
   - Create `voracms/public/js/admin.js` for all Twig admin templates (Quill, gallery, toggle AJAX, form validation, theme toggle, flash messages, swalConfirm)
   - Create `voracms/public/css/admin.css` additions for class-based styles replacing inline `style=""`
   - Add `voraRaymel/css/legal.css` for legal page styles (shared across privacitat/legal/cookies)
   - Extract shared gtag into `js/analytics.js` per site
   - Replace all `onclick`/`onmouseover`/`onmouseout` in voraRaymel and admin templates with external JS event listeners
   - Add shared `reset.css` and `root.css` links to every page
   - **Pros**: Single import per site, clean separation, easy to maintain, reduces JS duplication (toggle AJAX duplicated 3x)
   - **Cons**: Some files (admin.js) could become large; Quill and gallery JS is tightly coupled to specific markup
   - **Effort**: Medium-High

2. **Approach B — Component-based extraction**
   - Split admin JS into: `js/admin-quill.js`, `js/admin-gallery.js`, `js/admin-toggle.js`, `js/admin-form-validation.js`
   - Create `css/legal.css` with all legal page styles (shared)
   - **Pros**: Fine-grained, better for caching, independent modules
   - **Cons**: Many HTTP requests, more complex setup, overhead for small pages
   - **Effort**: High

3. **Approach C — Minimal surgical extraction**
   - Only extract the most egregious inline blocks (legal page `<style>`, admin Quill/gallery JS, voraRaymel onclick)
   - Leave small inline `style=""` and gtag in place
   - **Pros**: Fastest, lowest risk
   - **Cons**: Leaves many rule violations, misses `reset.css`/`root.css` linking, technical debt remains
   - **Effort**: Low-Medium

### Recommendation

**Approach A** with the following plan:

1. **VoraStudio site** (5 files):
   - Extract gtag into `VoraStudio/js/analytics.js` (single shared file, all pages use same snippet)
   - Extract Lenis init from `projectes.html` into its own function within `js/projectes-scroll.js`
   - Move inline styles from CTA buttons into CSS classes in `css/style.css`
   - Add `<link rel="stylesheet" href="css/reset.css">` and `<link rel="stylesheet" href="css/root.css">` to all pages
   - Note: `html/projectes.html` uses `../css/style.css` but other files use `css/style.css` — fix path inconsistency

2. **voracms admin** (15 templates):
   - Extract shared gtag into `js/analytics.js`
   - Move form validation from `base.html.twig` into `js/admin.js`
   - Move `swalConfirm`, flash messages, and theme toggle from `layout.html.twig` into `js/admin.js`
   - Move Quill init, gallery management, YouTube preview from `entry/edit.html.twig` and `entry/new.html.twig` into `js/admin.js`
   - Move toggle AJAX (duplicated 3x across entry/index, user/index) into `js/admin.js` as a shared function
   - Move auto-slug, field management, media picker, color picker snippets into `js/admin.js`
   - Add `<link rel="stylesheet" href="{{ asset('css/reset.css') }}">` to `base.html.twig`
   - Replace all `style=""` attributes with CSS classes in `admin.css`
   - Replace all `onmouseover`/`onmouseout` in `dashboard.html.twig` with CSS `:hover`

3. **voraRaymel site** (5 files):
   - Extract 3 inline `<style>` blocks (privacitat/legal/cookies) into `voraRaymel/css/legal.css`
   - Move footer inline `style="padding: 2rem 5%"` into CSS class
   - Replace all 10 `onclick` handlers (lang-switcher toggle + mobile menu close) with event listeners in `js/scripts.js`
   - Move `RECAPTCHA_SITE_KEY` inline JS into `js/scripts.js` (already partially there)
   - Add `<link rel="stylesheet" href="css/reset.css">` and `<link rel="stylesheet" href="css/root.css">`

4. **aulaGastronomica** (1 file):
   - Move inline `<script>` (js-enabled class) into `js/script.js`
   - Replace `style="display:block"` and `style="background-image"` with CSS classes in `css/styles.css`
   - Already links local `reset.css` — good. Add `root.css` reference if needed.

5. **Shared CSS tokens**:
   - Add `<link rel="stylesheet" href="css/root.css">` to every production page (currently zero pages link it despite it being the design token system)

### Risks

- **High risk**: Admin Quill editor initialization is tightly coupled to the HTML markup in entry templates. Extracting it to an external JS file requires careful testing — the Quill instances must be initialized after the DOM is ready AND after the Quill CDN loads.
- **Medium risk**: The media picker uses `window.postMessage` communication between `media/picker.html.twig` and `entry/*.html.twig`. Extracting these functions needs both the sender and receiver functions in the same scope.
- **Medium risk**: The 3 legal pages (privacitat, legal, cookies) share nearly identical `<style>` blocks with minor differences (cookies has table styles). A unified `legal.css` must accommodate all variations.
- **Low risk**: Inline `style=""` attributes on honeypot divs (`display:none`) are low risk to externalize.
- **Low risk**: voraRaymel `onclick` → event listener migration is straightforward but requires verifying no scoping issues with the mobile menu checkbox.
- **Missing reset.css/root.css**: Adding shared CSS links to all pages could cause visual regressions if existing styles depend on the lack of a reset. Test needed.
