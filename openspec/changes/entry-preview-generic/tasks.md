# Tasks: Entry Preview Genérico

## T1 — Crear preview_generic.html.twig
- **Path**: `voracms/templates/admin/entry/preview_generic.html.twig`
- **Disseny**: Cards independents per camp, amb label i valor, responsive
- **Field types**: text, textarea, richtext, image, gallery, youtube, boolean, number, date, datetime, url, email, color, location
- **Estat**: ✅ COMPLETAT

## T2 — Modificar EntryController::preview()
- **Path**: `voracms/src/Controller/Admin/EntryController.php`
- **Lògica**:
  1. Buscar template slug-specific (`preview_{slug}.html.twig`)
  2. Si no existeix, comprovar si és `vorastudio-projects` → legacy `preview.html.twig`
  3. Si no, renderitzar `preview_generic.html.twig`
- **Injecció**: Afegir `TwigEnvironment $twig` com a paràmetre per `getLoader()->exists()`
- **Estat**: ✅ COMPLETAT

## T3 — Verificació
- **Acció**: Obrir preview per a entrades de Notícies i Events
- **Criteri**: Cada camp es mostra amb el seu label i valor, en el format adequat per al tipus
- **Estat**: ⏳ Pendent de confirmació usuari
