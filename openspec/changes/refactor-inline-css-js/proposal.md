# Proposal: Refactor inline CSS/JS in Twig templates

## Intent

Eliminar todo CSS inline (`style=""`, `<style>` blocks) y JS inline (`<script>`, `onclick`, `onmouseover`) de los templates Twig del admin VoraCMS, migrándolos a archivos externos (`admin.css`, `admin.js`, `entry-form.js`). Además, componentizar patrones Twig repetidos en includes y añadir `reset.css` + `root.css` a la base layout. Refactor puro — cero cambios visuales o funcionales.

## Scope

### In Scope
- ~250 instancias de `style="..."` → `admin.css` (FASE 1)
- 8 componentes Twig reutilizables (section_header, stat_mini, badge, status, gallery_field, quill_field, toggle_btn, dashboard_stat_card) — FASE 2
- ~600 líneas de `<script>` inline → `admin.js` (swalConfirm, flash, theme, form validation) — FASE 3
- Lógica AJAX toggle compartida (entry/user/project) → `admin.js` — FASE 4
- Quill editor + galería + Youtube + media picker → `entry-form.js` — FASE 5
- `reset.css` + `root.css` en `base.html.twig` — FASE 6
- Eventos inline (`onclick`, `onmouseover`) → listeners JS — FASE 7

### Out of Scope
- Refactor CSS/Twig/JS no relacionados con inline removal
- Cambios visuales o funcionales
- Tests automatizados (proyecto no tiene setup de tests)
- Migración a frameworks JS

## Capabilities

### New Capabilities
None — pure refactor, zero spec-level behavior changes.

### Modified Capabilities
None — no requirements change, only implementation moves from inline to external files.

## Approach

7 fases secuenciales ejecutadas en orden de riesgo creciente:

1. **CSS inline** → extraer valores a clases CSS en `admin.css` (bajo riesgo, mecánico)
2. **Componentes Twig** → crear `_*.html.twig` en `admin/components/` y reemplazar patrones repetidos
3. **JS base** → migrar swalConfirm, flash messages, theme toggle, form validation a `admin.js`
4. **JS listados** → unificar toggle AJAX (3 templates share el mismo patrón)
5. **JS editor** → Quill + galería + Youtube + media picker a `entry-form.js`
6. **reset.css + root.css** → añadir link en `<head>` de `base.html.twig`
7. **Eventos inline** → reemplazar `onclick="..."` con `addEventListener` en JS

Cada fase termina con verificación visual manual (no hay tests).

## Affected Areas

| Area | Impact | Description |
|------|--------|-------------|
| `voracms/templates/*.twig` | Modified | 19 templates pierden inline CSS/JS |
| `voracms/public/css/admin.css` | Modified | Recibe ~250 valores de estilo extraídos |
| `voracms/public/js/admin.js` | New | JS unificado del admin |
| `voracms/public/js/entry-form.js` | New | JS del editor de entradas |
| `voracms/templates/admin/components/` | New | 8 Twig includes componentizados |

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| Rotura visual por CSS mal extraído | Medium | Verificar cada template después de FASE 1 |
| JS inline con dependencias de orden ocultas | Medium | Extraer respetando el orden original; probar cada funcionalidad |
| Quill init con config inline frágil | Low | Mantener config inline como data-atributos que entry-form.js lee |
| onclick que referencia funciones no globales | Low | Auditoría previa: todas las funciones referenciadas son globales |

## Rollback Plan

Cada fase es un commit independiente. Revertir el commit de la fase problemática restaura el estado anterior. Las fases no tienen dependencias de datos — el rollback es git-level.

## Dependencies

- Ninguna externa. Todo el stack es vanilla (Symfony + Twig + vanilla JS/CSS).

## Success Criteria

- [ ] Cero atributos `style="..."` en templates Twig del admin
- [ ] Cero `<script>` blocks inline en templates del admin
- [ ] Cero eventos inline (`onclick`, `onmouseover`, etc.)
- [ ] `base.html.twig` linkea `reset.css` y `root.css`
- [ ] admin.css NO contiene reglas que cambien la apariencia actual
- [ ] Todas las funcionalidades del admin funcionan: carga, editores, toggles, flash messages, tema
