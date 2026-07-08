# Tasks: Entry Preview Fidelitat

## T1 — Corregir referència CSS a preview.html.twig
- **Path**: `voracms/templates/admin/entry/preview.html.twig` (línia 12)
- **Canvi**: Substituir `/VoraStudio/css/style.css` per `http://localhost/VoraStudio/VoraStudio/css/style.css`
- **Raó**: El preview es serveix des de Symfony (localhost:8000) on la ruta /VoraStudio/ no existeix. El frontend CSS es serveix des d'Apache (localhost/VoraStudio/VoraStudio/css/style.css).
- **Estat**: ✅ COMPLETAT

## T2 — Reescriure preview-overrides.css
- **Path**: `voracms/public/css/admin/preview-overrides.css`
- **Canvi**: Eliminar totes les regles que alteren el layout del frontend:
  - Treure `max-width`, `margin-left/right: auto`, `padding-left/right`, `width: 100%` dels selectors `.project-hero`, `.project-strategy`, `.project-gallery`
  - Treure `background-color: #f5f5f5` del `.preview-wrapper`
  - Mantenir només `min-height: 100vh` i assegurar que `body.page-projecte` hereta el fons blanc del frontend
- **Raó**: El frontend usa `width: 100vw` i `padding: 180px 5vw 7rem`. Qualsevol override trenca la fidelitat visual.
- **Estat**: ✅ COMPLETAT

## T3 — Verificar animacions GSAP
- **Path**: `voracms/public/js/admin/preview.js`
- **Verificació**: Comparar amb `VoraStudio/js/dynamic-content.js` (funció `animateSections()`)
- **Resultat**: ✅ Ja són idèntiques (hero timeline, strategy stagger, gallery reveal)

## T4 — Verificació visual
- **Acció**: Obrir `http://localhost:8000/admin/entry/{id}/preview` per a una entrada de tipus vorastudio-projects
- **Criteri**: El disseny ha de ser píxel-per-píxel idèntic a `http://localhost/VoraStudio/VoraStudio/projectes/projecte.php?project=aurex` (hero + strategy + gallery)
- **Estat**: ⏳ Pendent de confirmació usuari
