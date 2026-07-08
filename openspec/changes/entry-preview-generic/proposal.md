# Proposal: Entry Preview Genérico

## Intent
El preview d'entrades només funcionava per al Content Type `vorastudio-projects` amb un template hardcodat. La resta de CTs (Notícies, Events, i qualsevol de futur) redirigien a la vista `show` sense cap previsualització visual. Cal un sistema de preview que funcioni per a **qualsevol** Content Type.

## Scope

### In Scope
- Modificar `EntryController::preview()` per rutear al template adequat segons el CT
- Crear `preview_generic.html.twig` que renderitzi tots els field types de forma dinàmica
- Suport per a field types: text, textarea, richtext, image, gallery, youtube, boolean, number, date, datetime, url, email, color, location
- Detectar automàticament templates específics per slug (ex: `preview_noticies.html.twig`)
- Mantenir compatibilitat amb el template legacy `preview.html.twig` per a `vorastudio-projects`

### Out of Scope
- Templates de preview personalitzats per a cada CT (es crearan sota demanda)
- Canvis al botó "Veure" de la llista d'entrades

## Approach
1. Controller: heurística de 3 nivells → slug-specific → legacy → generic fallback
2. Template genèric: cards de camp amb label + valor, adaptat per tipus (imatges, vídeos, galeries, booleans, dates, colors...)
3. Injecció de `Twig\Environment` per comprovar existència de templates via `getLoader()->exists()`

## Risks
- Cap. El fallback genèric cobreix qualsevol CT sense excepció
