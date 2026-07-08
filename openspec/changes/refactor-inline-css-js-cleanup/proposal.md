# Proposal: Refactor inline CSS/JS cleanup

## Intent

Zero remaining inline CSS/JS in voracms admin templates — follow-up to complete the `refactor-inline-css-js` change, which extracted ~250 inline styles and ~600 lines of inline JS but left ~20 static styles, ~9 dynamic Twig styles, 3 inline script blocks, and 3 redundant inline styles across 8 files.

## Scope

### In Scope
- **3 redundant inline styles** in `_gallery_field.html.twig` — remove (CSS class `.gallery-thumb` already provides identical values)
- **~20 static inline styles** → CSS classes in `admin.css` (8 templates: `project/index`, `user/index`, `entry/index`, `entry/show`, `project/show`, `content-type/index`, `_toggle_btn`)
- **~9 dynamic Twig inline styles** → CSS custom property pattern (`style="--prop:{{ value }}"`) across `project/index`, `project/show`, `entry/show`, `user/index`
- **~38 lines inline JS** from `content-type/edit.html.twig` + `content-type/new.html.twig` → shared external JS file (field clone/remove, auto-slug)
- **~43 lines inline JS** from `media/index.html.twig` → external JS with data-attribute config for `{{ path('admin_media_upload') }}`

### Out of Scope
- Google Analytics gtag in `base.html.twig` (accepted infrastructure exception)
- `display:none` config divs (3 instances — universal pattern, no visual impact)
- CSS custom properties already using `var(--token)` (already use design tokens)
- Refactors beyond inline removal

## Capabilities

### New Capabilities
None — pure refactor, zero spec-level behavior changes.

### Modified Capabilities
None — no requirements change, only implementation moves from inline to external.

## Approach

Apply same patterns from the completed refactor in order of risk:

1. **Redundant inline removals** — delete 3 values from `_gallery_field.html.twig` (trivial, class already covers them)
2. **Static inline styles** → new CSS classes in `admin.css` (mechanical, low risk)
3. **Dynamic Twig styles** → CSS custom properties (`--prop:{{ value }}`) + class using `var(--prop)`
4. **Content-type inline JS** → extract to `admin.js` (or new `content-type.js`) — clone/remove field rows, auto-slug
5. **Media inline JS** → extract to file with `data-upload-url` attribute on upload form element

## Affected Areas

| Area | Impact | Description |
|------|--------|-------------|
| `_gallery_field.html.twig` | Modified | Remove 3 redundant inline styles |
| `project/index.html.twig` | Modified | ~12 static + ~6 dynamic styles → CSS |
| `user/index.html.twig` | Modified | 7 static + 1 dynamic → CSS |
| `entry/index.html.twig` | Modified | 5 static → CSS |
| `entry/show.html.twig` | Modified | 1 static + 1 dynamic → CSS |
| `project/show.html.twig` | Modified | 1 static + 1 dynamic → CSS |
| `content-type/index.html.twig` | Modified | 2 static → CSS |
| `_toggle_btn.html.twig` | Modified | 2 static → CSS |
| `content-type/edit.html.twig` | Modified | 17-line JS block → external file |
| `content-type/new.html.twig` | Modified | 21-line JS block → external file |
| `media/index.html.twig` | Modified | 43-line JS block → external file with data attrs |
| `admin.css` | Modified | ~20 new CSS classes |
| `admin.js` | Modified | Appended content-type + media JS |

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| Dynamic Twig values break if property not passed | Low | Verify every template that uses `--prop:` passes the variable |
| `media/index.html.twig` JS depends on `{{ path() }}` | Low | Form `data-upload-url="...{{ path('admin_media_upload') }}..."` before script load |

## Rollback Plan

Single commit per group (CSS, content-type JS, media JS). Revert any commit independently.

## Dependencies

- None. Same vanilla stack (Symfony + Twig + vanilla CSS/JS).

## Success Criteria

- [ ] Zero inline `style="..."` attributes with static values in admin Twig templates
- [ ] Zero inline `<script>` blocks with JS logic in admin templates
- [ ] All dynamic Twig values use `--prop:{{ value }}` CSS custom property pattern
- [ ] Redundant inline styles removed from `_gallery_field.html.twig`
- [ ] All admin functionality unchanged: content-type field management, media upload, slug generation
