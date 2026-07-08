# Proposal: Entry Preview Fidelitat

## Intent
La previsualització d'entrades (Entry Preview) no es veia idèntica al frontend real. La pàgina `admin/entry/{id}/preview` carregava sense estils CSS del frontend i tenia overrides que alteraven el layout (max-width, padding, background). Cal fer que el disseny del preview sigui **píxel-per-píxel idèntic** al `projecte.php`.

## Scope

### In Scope
- Corregir la referència al CSS del frontend a `preview.html.twig` (ara apuntava a `/VoraStudio/css/style.css` que donava 404 des de Symfony)
- Reescriure `preview-overrides.css` per no alterar el layout original (eliminar max-width, padding constraint, background gris)
- Mantenir les mateixes animacions GSAP que el frontend

### Out of Scope
- Canvis estructurals al HTML del preview (ja coincidia 1:1 amb el frontend)
- Nou sistema de preview genèric per a tots els Content Types (tractat com a issue separat)
- Canvis al botó "Veure" (ja existeix)

## Approach
1. Substituir la ruta CSS absoluta `/VoraStudio/css/style.css` per la URL completa d'Apache `http://localhost/VoraStudio/VoraStudio/css/style.css`
2. Reescriure `preview-overrides.css`: eliminar tot override de layout (max-width, margin, padding, width) i només mantenir `min-height: 100vh` en el wrapper
3. `preview.js` ja és funcionalment idèntic a `dynamic-content.js` → no cal tocar-lo

## Risks
- La URL absoluta a Apache és específica de l'entorn de desenvolupament. Per producció caldrà una altra estratègia (CDN, asset symlink, o servir el CSS des del mateix Symfony)
