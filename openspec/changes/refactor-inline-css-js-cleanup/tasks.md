# Tasks: Refactor Inline CSS/JS Cleanup

## Review Workload Forecast

| Field | Value |
|-------|-------|
| Estimated changed lines | ~200 |
| 400-line budget risk | Low |
| Chained PRs recommended | No |
| Suggested split | Single PR |
| Delivery strategy | single-pr |
| Chain strategy | size-exception |

Decision needed before apply: Yes
Chained PRs recommended: No
Chain strategy: size-exception
400-line budget risk: Low

## Phase 1: Redundant Inline Removals

- [x] 1.1 Remove 3 duplicate inline styles from `_gallery_field.html.twig` (`.gallery-thumb` class already provides identical values)

## Phase 2: Static Inline Styles → CSS Classes

- [x] 2.1 Add ~20 utility CSS classes to `voracms/public/css/admin.css` (one class per extracted static `style` value)
- [x] 2.2 Replace ~18 inline styles in `voracms/templates/admin/project/index.html.twig` (12 static → class, 6 dynamic → `--prop:{{ value }}`)
- [x] 2.3 Replace ~8 inline styles in `voracms/templates/admin/user/index.html.twig` (7 static → class, 1 dynamic → `--prop`)
- [x] 2.4 Replace 5 inline styles in `voracms/templates/admin/entry/index.html.twig` (static → class)
- [x] 2.5 Replace ~2 inline styles in `voracms/templates/admin/entry/show.html.twig` (1 static → class, 1 dynamic → `--prop`)
- [x] 2.6 Replace ~2 inline styles in `voracms/templates/admin/project/show.html.twig` (1 static → class, 1 dynamic → `--prop`)
- [x] 2.7 Replace 2 inline styles in `voracms/templates/admin/content-type/index.html.twig` (static → class)
- [x] 2.8 Replace 2 inline styles in `voracms/templates/admin/components/_toggle_btn.html.twig` (static → class)

## Phase 3: Content-Type Inline JS Extraction

- [x] 3.1 Create `voracms/public/js/content-type.js` with field clone/remove and auto-slug generation functions
- [x] 3.2 Replace inline `<script>` in `content-type/edit.html.twig` with `{{ asset('js/content-type.js') }}` include
- [x] 3.3 Replace inline `<script>` in `content-type/new.html.twig` with same asset include

## Phase 4: Media Index Inline JS Extraction

- [x] 4.1 Add `data-upload-url="{{ path('admin_media_upload') }}"` attribute to upload form in `media/index.html.twig`
- [x] 4.2 Create `voracms/public/js/media-index.js` with upload AJAX, file preview, and copy URL functions
- [x] 4.3 Replace inline `<script>` in `media/index.html.twig` with `{{ asset('js/media-index.js') }}` include

## Phase 5: Verification

- [x] 5.1 Verify gallery thumbnails render correctly without redundant inline styles
- [x] 5.2 Verify all 8 admin template pages display correctly with CSS classes
- [x] 5.3 Verify content-type field clone/remove and auto-slug still work
- [x] 5.4 Verify media upload, file preview, and copy URL flow works
