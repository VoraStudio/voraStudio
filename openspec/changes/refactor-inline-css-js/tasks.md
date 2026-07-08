# Tasks: Refactor inline CSS/JS in Twig templates

## Review Workload Forecast

| Field | Value |
|-------|-------|
| Estimated changed lines | ~550-750 |
| 400-line budget risk | High |
| Chained PRs recommended | Yes |
| Suggested split | PR 1 (FASES 1-2) → PR 2 (FASES 3-5) → PR 3 (FASES 6-7) |
| Delivery strategy | size:exception |
| Chain strategy | single-pr |

## Phase 1: CSS Extraction

- [x] 1.1 Scan 16 templates for `style="..."` instances (~250) and catalog each as a CSS class candidate
- [x] 1.2 Add classes to `admin.css` for all extracted inline styles (dashboard stats, cards, list items, forms)
- [x] 1.3 Replace inline `style="..."` in dashboard.html.twig (metrics, cyber-cards, user/project/section lists)
- [x] 1.4 Replace inline `style="..."` in entry/new.html.twig and entry/edit.html.twig (fields, file inputs, gallery thumbs)
- [x] 1.5 Replace inline `style="..."` in entry/index.html.twig, user/index.html.twig, project/index.html.twig
- [x] 1.6 Replace inline `style="..."` in content-type/new.html.twig, content-type/edit.html.twig, content-type/index.html.twig
- [x] 1.7 Replace inline `style="..."` in media/index.html.twig and media/picker.html.twig
- [x] 1.8 Replace inline `style="..."` in project/form.html.twig, project/show.html.twig, user/form.html.twig, login.html.twig
- [x] 1.9 Extract inline `<style>` block from media/picker.html.twig into `admin.css` with `.media-picker-*` classes

## Phase 2: Twig Components

- [x] 2.1 Create `admin/components/_section_header.html.twig` — reusable header with icon, title, desc, right-actions
- [x] 2.2 Create `admin/components/_stat_mini.html.twig` — mini stat with value, label, optional active modifier
- [x] 2.3 Create `admin/components/_badge.html.twig` — status badge with color mapping (published/draft/archived)
- [x] 2.4 Create `admin/components/_status.html.twig` — active/inactive toggle indicator
- [x] 2.5 Create `admin/components/_gallery_field.html.twig` — gallery file upload + preview + media picker
- [x] 2.6 Create `admin/components/_quill_field.html.twig` — Quill editor wrapper with hidden textarea
- [x] 2.7 Create `admin/components/_toggle_btn.html.twig` — AJAX toggle button for active/visibility state
- [x] 2.8 Create `admin/components/_dashboard_stat_card.html.twig` — stat card with icon, label, value, optional link
- [x] 2.9 Replace duplicate patterns in all templates with the new components

## Phase 3: JS Base

- [x] 3.1 Create `public/js/admin.js` with `swalConfirm()` function (from layout.html.twig)
- [x] 3.2 Extract flash messages → SweetAlert toast logic to `admin.js`
- [x] 3.3 Extract theme toggle logic (localStorage, data-theme) to `admin.js`
- [x] 3.4 Extract form validation (novalidate, Quill check, scroll) from base.html.twig to `admin.js`
- [x] 3.5 Google Analytics gtag snippet retained in base.html.twig (non-admin block)
- [x] 3.6 Add `<script src="{{ asset('js/admin.js') }}">` to layout.html.twig

## Phase 4: JS Listados

- [x] 4.1 Extract identical toggle AJAX logic from entry/index, user/index, project/index to `admin.js`
- [x] 4.2 Add event delegation in `admin.js` for `.toggle-btn` click handler
- [x] 4.3 Remove duplicate `<script>` blocks from the 3 list templates

## Phase 5: JS Editor

- [x] 5.1 Create `public/js/entry-form.js` with Quill initialization + toolbar config
- [x] 5.2 Add YouTube preview + extractYoutubeId to `entry-form.js`
- [x] 5.3 Add gallery/image preview on file pick to `entry-form.js`
- [x] 5.4 Add gallery item remove handler (delegated) to `entry-form.js`
- [x] 5.5 Add media picker modal (`pick-media` click handler) to `entry-form.js`
- [x] 5.6 Add `addMediaToGallery()` + postMessage handler to `entry-form.js`
- [x] 5.7 Add Quill sync on form submit to `entry-form.js`
- [x] 5.8 Add `<script src="{{ asset('js/entry-form.js') }}">` to entry/new.html.twig and entry/edit.html.twig
- [x] 5.9 Remove inline `<script>` blocks from entry/new.html.twig, entry/edit.html.twig, media/picker.html.twig, media/index.html.twig

## Phase 6: reset.css + root.css

- [x] 6.1 Create `public/css/reset.css` — modern CSS reset (box-sizing, margins, font smoothing)
- [x] 6.2 Create `public/css/root.css` — CSS custom properties (colors, spacing, radii, transitions, z-index tokens)
- [x] 6.3 Add `<link rel="stylesheet" href="{{ asset('css/reset.css') }}">` and `root.css` to `base.html.twig`
- [x] 6.4 Move existing `:root` block from `admin.css` into `root.css`
- [x] 6.5 Verify all templates reference the correct CSS files

## Phase 7: Inline Events Cleanup

- [x] 7.1 Replace `onclick="..."` in entry/index.html.twig (toggle/delete buttons) with event delegation in `admin.js`
- [x] 7.2 Replace `onclick="..."` in user/index.html.twig, project/index.html.twig, content-type/index.html.twig
- [x] 7.3 Replace `onclick="..."` in media/index.html.twig (delete buttons)
- [x] 7.4 Replace `onmouseover`/`onmouseout` in dashboard.html.twig (list item hover) with CSS `:hover` classes
- [x] 7.5 Replace inline `onclick="window.close()"` in media/picker.html.twig with JS listener
- [x] 7.6 Replace inline `onclick` in layout.html.twig sidebar toggle with JS listener
- [x] 7.7 Final sweep: grep for `onclick=`, `onmouseover=`, `onmouseout=`, `style="`, `<script>`, `<style>` — confirm zero in final output
