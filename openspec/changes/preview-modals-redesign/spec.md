# Spec: Preview Modals Redesign

## Summary
Redesign the preview modals for Noticia and Event content types in VoraCMS. Improve visibility, add theme support, and fix rendering artifacts.

## Scope
- `templates/admin/entry/preview_noticia.html.twig` — Noticia preview
- `templates/admin/entry/preview_event.html.twig` — Event preview
- `public/js/admin.js` — Modal JS (theme passthrough)
- `public/css/admin.css` — Modal overlay + header theme variables

## Requirements

### R1: Pill/tag contrast (Noticia)
- Category tags (`.m9-tag`): semi-transparent dark background with white text, readable on any hero image
- Date/location pills (`.m9-pill`): light blue backgrounds with dark text, WCAG AA compliant

### R2: Text readability (Noticia)
- Body text line-height reduced to 1.1

### R3: Card border rendering
- No darkened bottom border caused by `overflow: hidden` + `border-radius` anti-aliasing artifact
- Use `overflow: clip` instead of `overflow: hidden`

### R4: Theme adaptation
- Preview must respect CMS theme selection (light/dark), not `prefers-color-scheme`
- Theme passed via `?theme=` URL parameter from admin.js
- CSS uses `:root[data-theme="dark"]` selectors

### R5: Card shadow
- No external box-shadow on card to avoid directional darkening of bottom border

### R6: Event preview redesign
- Replace split bicolor layout (m10) with header+grid layout (m6)
- Orange gradient → brand blue gradient `#4945ff → #3733cc`
- Rectangular card (no border-radius)
- Two-column grid: text left, images right
- Adaptive gallery: no empty placeholders, layout adapts to 1-4 images

### R7: Modal overlay
- Semi-transparent overlay with backdrop-filter blur
- Light mode: `rgba(0,0,0,0.1)` + `blur(10px)`
