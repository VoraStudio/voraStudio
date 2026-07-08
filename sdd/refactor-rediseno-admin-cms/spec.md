# Spec: refactor/rediseno-admin-cms

## Capabilities

### va-design-system

- **REQ-001**: Todas las clases CSS del admin usan el prefijo `va-` y siguen la metodología BEM.
  - **Descripción**: Se unifican los sistemas `s-*`, `cyber-*` y clases sueltas bajo una única librería `va-*`. Los modificadores usan BEM (`.va-btn--primary`, `.va-input--error`).
  - **Criterio de aceptación**: `grep -R "class=\"s-" templates/admin/` y `grep -R "class=\"cyber-" templates/admin/` devuelven 0 resultados. Toda clase de UI admin comienza con `va-`.
  - **Templates afectados**: Los 39 templates admin (layout, login, dashboard, entry, content-type, base-content, project, user, media, components, api-guide, previews).
  - **Escenario de prueba**: Al inspeccionar `templates/admin/entry/edit.html.twig`, todas las clases son `va-form`, `va-input`, `va-panel`, etc.; no aparece `s-field` ni `cyber-card`.

- **REQ-002**: La hoja de estilos monolítica `public/css/admin.css` se divide en 8 archivos bajo `public/css/admin/`.
  - **Descripción**: Se elimina `admin.css` (6.804 líneas) y se crean `root.css`, `layout.css`, `forms.css`, `components.css`, `tables.css`, `dashboard.css`, `login.css` y `theme.css`.
  - **Criterio de aceptación**: Existen los 8 archivos, `public/css/admin.css` ha sido eliminado tras la verificación, y la suma total de líneas es inferior a 5.000.
  - **Templates afectados**: `templates/admin/layout.html.twig`, `templates/admin/login.html.twig`, `templates/base.html.twig` (referencias de asset).
  - **Escenario de prueba**: `ls -l voracms/public/css/admin/` lista los 8 archivos; `test -f voracms/public/css/admin.css` falla.

- **REQ-003**: Los design tokens se centralizan en `public/css/admin/root.css` bajo el prefijo `--va-*`.
  - **Descripción**: Se renombran/crean variables CSS para colores, espaciado (escala 8 px), radios, sombras, transiciones, z-index, tipografía y dimensiones de layout. Ningún valor hardcodeado se repite en los demás archivos.
  - **Criterio de aceptación**: Todos los selectores de los 8 archivos usan variables `--va-*`. `grep -R "#0c0e24\|#141418\|rgba(255,255,255,0.06)" public/css/admin/*.css` (salvo en root.css) devuelve 0.
  - **Templates afectados**: Ninguno directamente; consumido por todos los templates admin vía layout.
  - **Escenario de prueba**: Cambiar `--va-bg-page` en `root.css` altera el fondo de todas las páginas admin sin tocar otro archivo.

- **REQ-004**: Cada clase CSS `va-*` se define exactamente una vez en todo el proyecto (Rule #18).
  - **Descripción**: No se duplican selectores. Las variaciones se resuelven con modificadores BEM (`.va-card--dark`) o clases utilitarias.
  - **Criterio de aceptación**: Para cada clase `.va-X`, `grep -c "\.va-X" public/css/admin/*.css` devuelve 1. No existen selectores idénticos repetidos.
  - **Templates afectados**: Todos los templates admin.
  - **Escenario de prueba**: Buscar `.va-table__row` en los 8 CSS devuelve una única definición; `.va-table__row--inactive` es el modificador para filas inactivas.

- **REQ-005**: Cada clase tiene su comportamiento responsive agrupado en una única media query (Rule #17).
  - **Descripción**: Si una clase necesita overrides en múltiples breakpoints, todos viven dentro de un solo bloque `@media` al final del archivo o en una sección dedicada. No se dispersan reglas responsive de un mismo selector.
  - **Criterio de aceptación**: Para cada clase `.va-X`, sus reglas `@media` aparecen en un único bloque. `grep -A 50 "@media" file.css` muestra todos los overrides de `.va-X` juntos.
  - **Templates afectados**: Todos los templates admin.
  - **Escenario de prueba**: `.va-form__grid` cambia a 1 columna en un único `@media (max-width: ...)`; no hay otro `@media` más abajo que la toque.

### va-theme

- **REQ-010**: El tema por defecto del admin es oscuro y no requiere atributo `data-theme`.
  - **Descripción**: `:root` define los tokens del modo oscuro. Todos los componentes heredan estas variables sin necesidad de clase adicional.
  - **Criterio de aceptación**: Con `html` sin `data-theme`, la interfaz se ve en modo oscuro; fondo de página `--va-bg-page` apunta a un color oscuro.
  - **Templates afectados**: Todos.
  - **Escenario de prueba**: Abrir `/admin/dashboard` en incógnito muestra fondo oscuro, texto claro y inputs con `rgba(255,255,255,0.06)`.

- **REQ-011**: El tema claro se activa únicamente con `[data-theme="light"]` en `html`.
  - **Descripción**: `theme.css` contiene un único bloque `[data-theme="light"]` que reasigna variables CSS. No define selectores de componentes.
  - **Criterio de aceptación**: `theme.css` solo contiene el selector `[data-theme="light"]` y reasignaciones de variables. No contiene `.va-*`.
  - **Templates afectados**: `templates/admin/layout.html.twig`, `public/css/admin/theme.css`.
  - **Escenario de prueba**: Al cambiar `data-theme="light"`, el fondo pasa a claro y los inputs a blanco sin que ninguna clase de componente cambie.

- **REQ-012**: No hay duplicación de selectores entre temas oscuro y claro.
  - **Descripción**: Los componentes no tienen variantes `.va-input--light`; el cambio de tema es puramente por variables.
  - **Criterio de aceptación**: `grep -E "\.va-.*--light|\.va-.*\.theme-light|body\.light" public/css/admin/*.css` devuelve 0.
  - **Templates afectados**: Todos.
  - **Escenario de prueba**: En inspección de elementos, `.va-input` mantiene la misma clase en ambos temas; solo cambian los valores computados de las variables.

- **REQ-013**: El tema seleccionado persiste entre navegación y recargas.
  - **Descripción**: El toggle de tema guarda la preferencia en `localStorage` y la aplica antes del primer render para evitar flash de tema incorrecto.
  - **Criterio de aceptación**: Tras cambiar a light, recargar `/admin/entries` mantiene light. No se observa flash oscuro previo.
  - **Templates afectados**: `templates/admin/layout.html.twig`, `public/js/admin.js`.
  - **Escenario de prueba**: Usuario pulsa "Modo claro", navega a dashboard, recarga: sigue en claro. Cerrar y volver a abrir el navegador mantiene la preferencia.

### va-form-composition

- **REQ-020**: Los formularios de edición combinan el estilo 01 (Soft Dark) para inputs/composición con el estilo 10 (Notion) para layout documento + sidebar.
  - **Descripción**: El área principal es una card con fondo oscuro sutil y campos con `rgba(255,255,255,0.06)`. El panel lateral derecho es sticky y contiene metadatos, estado y acciones.
  - **Criterio de aceptación**: `templates/admin/entry/edit.html.twig` usa `.va-form__document` (estilo 10) y `.va-form__meta` sticky, mientras que `.va-input` sigue los tokens del estilo 01.
  - **Templates afectados**: `templates/admin/entry/{new,edit,show}.html.twig`, `templates/admin/content-type/{new,edit}.html.twig`, `templates/admin/base-content/{new,edit,show}.html.twig`, `templates/admin/project/form.html.twig`, `templates/admin/user/form.html.twig`.
  - **Escenario de prueba**: En desktop, el panel lateral de metadatos permanece visible al hacer scroll en el formulario largo.

- **REQ-021**: El panel lateral de metadatos es sticky en desktop y apilado en mobile.
  - **Descripción**: El sidebar de metadatos usa `position: sticky` con `top` definido por token; en viewports estrechos pasa a flujo vertical sobre o bajo el contenido.
  - **Criterio de aceptación**: A 1440 px el panel lateral es sticky; a 720 px o menos es estático y ocupa el ancho completo.
  - **Templates afectados**: Formularios de edición listados en REQ-020.
  - **Escenario de prueba**: Hacer scroll en `/admin/entry/123/edit` a 1440 px mantiene visible el panel "Estat"; a 375 px el panel aparece apilado arriba o abajo.

- **REQ-022**: Los campos de formulario se organizan en una cuadrícula de 2 columnas que colapsa a 1 columna en mobile.
  - **Descripción**: Pares de campos (título/subtítulo, fecha/ubicación, inicio/fin) se muestran en 2 columnas. Por debajo del breakpoint móvil se apilan en 1 columna.
  - **Criterio de aceptación**: `.va-form__row--cols-2` tiene `grid-template-columns: 1fr 1fr` en desktop y `1fr` en mobile. Todos los campos pareados usan esta clase.
  - **Templates afectados**: Formularios de edición.
  - **Escenario de prueba**: Redimensionar `/admin/content-type/new` de 1440 px a 375 px: los campos pareados pasan de lado a lado a apilados verticalmente.

- **REQ-023**: El selector de estado de publicación vive dentro del formulario, no en la sidebar global de navegación.
  - **Descripción**: Se elimina cualquier control de estado de la sidebar. El estado se muestra en el header del formulario (segment control) y/o en el panel lateral de metadatos (estilo 01).
  - **Criterio de aceptación**: `templates/admin/layout.html.twig` no contiene controles de estado de contenido. `templates/admin/entry/edit.html.twig` muestra el estado en `.va-form__header` o `.va-form__meta`.
  - **Templates afectados**: `templates/admin/layout.html.twig`, formularios de edición.
  - **Escenario de prueba**: Navegar entre entries distintas no altera la sidebar global; el estado cambia solo dentro del formulario de cada entry.

## Layout

- **REQ-100**: La sidebar de navegación es colapsable en mobile mediante un toggle.
  - **Descripción**: En viewports estrechos la sidebar se oculta fuera de pantalla y un botón `.va-topbar__menu-toggle` la desliza. El overlay opcional cierra al pulsar fuera.
  - **Criterio de aceptación**: A 320 px la sidebar está oculta; pulsar el toggle la muestra con animación. A 1024 px la sidebar es visible fija.
  - **Templates afectados**: `templates/admin/layout.html.twig`.
  - **Escenario de prueba**: Abrir `/admin/dashboard` en iPhone SE (375 px): solo se ve el topbar. Pulsar "Menú" desliza la sidebar. Pulsar fuera/cierra.

- **REQ-101**: El topbar es sticky y contiene título de sección, toggle de tema y tarjeta de usuario.
  - **Descripción**: El topbar se mantiene fijo al hacer scroll. Incluye breadcrumb/título, botón de tema y el componente de usuario con avatar y menú desplegable.
  - **Criterio de aceptación**: `position: sticky` con `--va-topbar-height`. Título visible, botón tema funcional, `.va-user-card` presente.
  - **Templates afectados**: `templates/admin/layout.html.twig`, `templates/admin/components/_user_card.html.twig`.
  - **Escenario de prueba**: Hacer scroll en una lista larga: el topbar permanece visible. Pulsar el avatar muestra opciones de perfil/logout.

- **REQ-102**: El área de contenido principal tiene padding responsive.
  - **Descripción**: `.va-content` aplica `--va-space-*` escalado: menor en móvil, mayor en desktop. Respeta el ancho de sidebar en desktop.
  - **Criterio de aceptación**: Padding mobile ≥ 16 px, desktop ≥ 32 px. Margen izquierdo en desktop igual a `--va-sidebar-width`.
  - **Templates afectados**: `templates/admin/layout.html.twig`.
  - **Escenario de prueba**: A 320 px el contenido tiene padding lateral de 16 px. A 1440 px tiene 32-48 px y deja espacio a la sidebar.

- **REQ-103**: El footer del admin es simple y no invasivo.
  - **Descripción**: Footer con copyright/marca y enlaces mínimos, al final del viewport si hay poco contenido o al final del documento si es largo.
  - **Criterio de aceptación**: `.va-footer` existe en layout, con texto secundario y sin fixed positioning.
  - **Templates afectados**: `templates/admin/layout.html.twig`.
  - **Escenario de prueba**: En una página con poco contenido, el footer está al fondo de la ventana. En una lista larga, aparece tras el scroll.

## Forms

- **REQ-200**: Los inputs base usan fondo oscuro semitransparente, borde sutil y estado focus visible.
  - **Descripción**: Basado en estilo 01 Soft Dark: `background: rgba(255,255,255,0.06)`, `border: 1px solid rgba(255,255,255,0.11)`, `border-radius: var(--va-radius-md)`. En focus se aclara el fondo y el borde.
  - **Criterio de aceptación**: `.va-input` cumple los valores de token. En focus aplica `--va-border-focus` y `--va-bg-input-focus`.
  - **Templates afectados**: Todos los formularios de edición.
  - **Escenario de prueba**: Hacer focus en el campo "Título" de `/admin/entry/new`: el borde se ilumina y el fondo cambia sutilmente.

- **REQ-201**: Los selects comparten el mismo estilo visual que los inputs de texto.
  - **Descripción**: `.va-select` tiene idéntica altura, padding, fondo, borde y focus que `.va-input`. Incluye flecha custom.
  - **Criterio de aceptación**: Un select y un input adyacentes miden lo mismo y comparten tokens `--va-input-bg`, `--va-input-border`, `--va-radius-md`.
  - **Templates afectados**: Formularios con selects (content-type, project, user, entry).
  - **Escenario de prueba**: Inspeccionar un select y un input en `/admin/project/form`: mismas dimensiones y estados.

- **REQ-202**: Las textareas tienen altura mínima y permiten resize vertical.
  - **Descripción**: `.va-textarea` define `min-height` (p. ej. 120 px) y `resize: vertical`. Hereda estilo de input.
  - **Criterio de aceptación**: `min-height` definido por token; `resize: vertical`; no usa `resize: none` salvo casos justificados.
  - **Templates afectados**: Formularios con descripción/contenido largo.
  - **Escenario de prueba**: El campo descripción en `/admin/entry/edit` se puede agrandar verticalmente desde la esquina inferior derecha.

- **REQ-203**: Los checkboxes y radios usan estilos custom accesibles.
  - **Descripción**: Se ocultan los inputs nativos y se dibuja un control custom con `:checked`, estados focus visibles y soporte para teclado.
  - **Criterio de aceptación**: `.va-checkbox` y `.va-radio` tienen estado checked, hover y focus. Funcionan con teclado (Tab + Space/Enter).
  - **Templates afectados**: Formularios que los usen.
  - **Escenario de prueba**: Navegar con Tab hasta un checkbox y pulsar Espacio lo marca; el estilo visual cambia.

- **REQ-204**: Las zonas de upload usan borde dashed, icono y estado hover.
  - **Descripción**: `.va-upload` tiene `border-style: dashed`, icono, texto e hint. En hover cambian borde y fondo. Click en toda la zona abre el selector de archivo.
  - **Criterio de aceptación**: Border dashed con token; hover aplica `--va-bg-upload-hover` y `--va-border-upload-hover`; input file oculto.
  - **Templates afectados**: `templates/admin/entry/{new,edit}.html.twig`, `templates/admin/components/_gallery_field.html.twig`, `templates/admin/media/picker.html.twig`.
  - **Escenario de prueba**: Pasar el ratón sobre "Seleccionar fitxer" cambia el borde. Hacer click abre el diálogo de archivo.

- **REQ-205**: La galería muestra miniaturas en grid con zona de upload integrada.
  - **Descripción**: `.va-gallery` presenta thumbs en grid responsive. Cada thumb tiene acciones (eliminar, reordenar) y la zona de añadir más archivos ocupa una celda.
  - **Criterio de aceptación**: Grid de thumbs con `object-fit: cover`; celda de upload con el mismo estilo dashed. Acciones accesibles con `aria-label`.
  - **Templates afectados**: `templates/admin/components/_gallery_field.html.twig`, `templates/admin/media/index.html.twig`.
  - **Escenario de prueba**: Subir 5 imágenes: aparecen en filas de 4 en desktop y 2 en mobile. Cada thumb tiene botón eliminar con aria-label.

- **REQ-206**: El panel de estado dentro del formulario sigue el estilo 01 Soft Dark.
  - **Descripción**: Panel lateral/conjunto de opciones de estado con fondo `#0f0f13`, opciones con dot de color, opción seleccionada con bg más claro y borde sutil.
  - **Criterio de aceptación**: `.va-status-panel` usa tokens oscuros. Opciones con `.va-status-option` y modificadores `--draft`, `--published`, `--archived`.
  - **Templates afectados**: Formularios de edición.
  - **Escenario de prueba**: En `/admin/entry/edit`, el panel "Estat" muestra las 3 opciones con dot gris/verde/naranja; la activa tiene fondo más claro.

- **REQ-207**: Los campos pareados se muestran en grid de 2 columnas responsive.
  - **Descripción**: Ver REQ-022. Aplica a todos los pares de campos de formulario.
  - **Criterio de aceptación**: Todos los `.va-form__row--cols-2` colapsan a 1 columna bajo el breakpoint mobile.
  - **Templates afectados**: Formularios de edición.
  - **Escenario de prueba**: Ver REQ-022.

## Segment controls / Tabs de estado

- **REQ-300**: El segment control de estado se ubica en el header del formulario.
  - **Descripción**: Basado en estilo 06 Clean Light. Un grupo de 3 botones en línea dentro de `.va-form__header`, con fondo de contenedor `#f0f0f0` (adaptado a tokens oscuros en modo default) y opción seleccionada destacada.
  - **Criterio de aceptación**: `.va-segment` existe en el header. No está en la sidebar global. Contenedor usa `--va-bg-segment`, seleccionado usa `--va-bg-segment-selected`.
  - **Templates afectados**: Formularios de edición.
  - **Escenario de prueba**: En `/admin/entry/edit`, justo debajo del título aparecen 3 pestañas: "No publicat", "Publicat", "Arxivat".

- **REQ-301**: El segment control ofrece exactamente tres opciones: No publicado, Publicado, Archivado.
  - **Descripción**: Las opciones mapean los estados del CMS. Cada opción tiene dot de color correspondiente.
  - **Criterio de aceptación**: Solo hay 3 botones. Textos: "No publicat", "Publicat", "Arxivat". Valores: draft, published, archived.
  - **Templates afectados**: Formularios de edición.
  - **Escenario de prueba**: Inspeccionar el segment control: 3 `button` con `data-value` draft/published/archived.

- **REQ-302**: El botón seleccionado del segment control tiene fondo blanco y sombra en light, y fondo más claro en dark.
  - **Descripción**: En modo claro, el seleccionado es blanco con sombra suave. En modo oscuro, el seleccionado usa un tono más claro del fondo del segment sin sombra agresiva.
  - **Criterio de aceptación**: `.va-segment__option--selected` aplica `--va-bg-segment-selected` y `--va-shadow-segment-selected` (light) / sin sombra (dark).
  - **Templates afectados**: Formularios de edición.
  - **Escenario de prueba**: Cambiar entre light y dark: en light el seleccionado es blanco con sombra; en dark es gris claro sobre fondo gris oscuro.

## Tables

- **REQ-400**: La cabecera de tabla tiene fondo sutil.
  - **Descripción**: `.va-table__header` usa `--va-bg-table-header`, distinto pero armónico con el fondo de página. Texto en `--va-text-secondary`.
  - **Criterio de aceptación**: Header visualmente distinguible del body pero sin alto contraste excesivo. No usa imágenes ni gradientes.
  - **Templates afectados**: `templates/admin/entry/index.html.twig`, `templates/admin/content-type/index.html.twig`, `templates/admin/base-content/index.html.twig`, `templates/admin/project/index.html.twig`, `templates/admin/user/index.html.twig`, `templates/admin/media/index.html.twig`.
  - **Escenario de prueba**: La tabla de entries muestra la fila de cabecera con fondo ligeramente diferente al resto.

- **REQ-401**: Las filas de tabla tienen estado hover.
  - **Descripción**: `.va-table__row` cambia de fondo al pasar el ratón. Las filas inactivas tienen fondo/atenuación distinta.
  - **Criterio de aceptación**: Hover aplica `--va-bg-row-hover`. Filas inactivas usan `.va-table__row--inactive`.
  - **Templates afectados**: Templates de listado.
  - **Escenario de prueba**: Pasar el ratón por una fila de `/admin/entries` resalta la fila completa.

- **REQ-402**: Las tablas muestran badges de estado activo/inactivo.
  - **Descripción**: `.va-badge` con modificadores `--success`, `--warning`, `--danger`, `--neutral` indica el estado de cada registro.
  - **Criterio de aceptación**: Cada fila con estado tiene un badge. Los colores cumplen contraste WCAG AA con su fondo.
  - **Templates afectados**: Templates de listado.
  - **Escenario de prueba**: Entries publicados muestran badge verde "Publicat"; archivadas, naranja "Arxivat"; borradores, gris "No publicat".

- **REQ-403**: Las celdas de acción contienen botones de acción claros.
  - **Descripción**: Botones icono o texto para ver, editar, eliminar. Tienen `aria-label` si son iconos.
  - **Criterio de aceptación**: `.va-table__actions` agrupa los botones. Cada botón icono tiene `aria-label`. Espaciado de 8 px.
  - **Templates afectados**: Templates de listado.
  - **Escenario de prueba**: Cada fila de `/admin/users` tiene botones "Editar" y "Eliminar" con icono y aria-label.

- **REQ-404**: Las tablas son responsive mediante scroll horizontal en mobile.
  - **Descripción**: En viewports estrechos la tabla no se deforma; su contenedor `.va-table-wrapper` permite scroll horizontal.
  - **Criterio de aceptación**: A 375 px la tabla es más ancha que la pantalla y se desplaza horizontalmente dentro de su wrapper. No se oculta contenido ni se rompen celdas.
  - **Templates afectados**: Templates de listado.
  - **Escenario de prueba**: Abrir `/admin/entries` en móvil: se puede deslizar horizontalmente para ver todas las columnas.

## Dashboard

- **REQ-500**: Las tarjetas de estadísticas muestran icono, valor y etiqueta.
  - **Descripción**: `.va-stat-card` tiene icono (SVG o emoji neutral), número grande y etiqueta descriptiva. Usa fondo de card y borde sutil.
  - **Criterio de aceptación**: Cada card contiene `.va-stat-card__icon`, `.va-stat-card__value`, `.va-stat-card__label`. Icono no es emoji; es SVG o icon font.
  - **Templates afectados**: `templates/admin/dashboard.html.twig`, `templates/admin/components/_dashboard_stat_card.html.twig`.
  - **Escenario de prueba**: Dashboard muestra cards: "Entrades totals: 42", "Usuaris: 7", etc., cada una con icono SVG.

- **REQ-501**: La cuadrícula de métricas es responsive.
  - **Descripción**: `.va-metrics-grid` usa CSS Grid con 4 columnas en desktop, 2 en tablet y 1 en mobile.
  - **Criterio de aceptación**: Breakpoints claros en una única media query por clase. No hay cards cortadas ni desbordamiento.
  - **Templates afectados**: `templates/admin/dashboard.html.twig`.
  - **Escenario de prueba**: Redimensionar dashboard: 4 cards por fila a 1440 px, 2 a 768 px, 1 a 375 px.

- **REQ-502**: El dashboard usa codificación de color por estado.
  - **Descripción**: Métricas o listas usan colores semánticos (verde publicado, naranja archivado, gris borrador) consistentes con badges y segment control.
  - **Criterio de aceptación**: Los mismos tokens `--va-success`, `--va-warning`, `--va-danger`, `--va-text-muted` se usan en dashboard, tablas y badges.
  - **Templates afectados**: `templates/admin/dashboard.html.twig`, `templates/admin/components/_stat_mini.html.twig`.
  - **Escenario de prueba**: La métrica "Publicades" usa verde; "Arxivades", naranja; "Esborranys", gris.

## Login

- **REQ-600**: La página de login muestra una tarjeta centrada con logo/marca.
  - **Descripción**: `.va-login` centra vertical y horizontalmente una card con logo, título y formulario. Fondo oscuro (o claro según tema) sin sidebar ni topbar.
  - **Criterio de aceptación**: `/admin/login` no carga layout.html.twig. La card está centrada, con ancho máximo definido por token.
  - **Templates afectados**: `templates/admin/login.html.twig`, `public/css/admin/login.css`.
  - **Escenario de prueba**: Abrir `/admin/login`: solo se ve la card centrada con logo y campos de email/contraseña.

- **REQ-601**: El formulario de login usa inputs del sistema de diseño `va-*`.
  - **Descripción**: Campos de email y password usan `.va-input`. Botón de submit usa `.va-btn--primary`.
  - **Criterio de aceptación**: No hay clases `s-*` ni estilos inline. Los inputs heredan tokens de `forms.css`.
  - **Templates afectados**: `templates/admin/login.html.twig`.
  - **Escenario de prueba**: El campo email tiene el mismo estilo focus que los inputs del resto del admin.

- **REQ-602**: El login muestra estado de error accesible.
  - **Descripción**: Si las credenciales fallan, aparece `.va-alert--error` con mensaje claro, role="alert" y foco automático.
  - **Criterio de aceptación**: El mensaje de error es visible, usa `--va-danger`, tiene `role="alert"` y no usa color como único indicador.
  - **Templates afectados**: `templates/admin/login.html.twig`.
  - **Escenario de prueba**: Introducir credenciales incorrectas muestra "Credencials incorrectes" en rojo con icono de error.

## Theme

- **REQ-700**: El topbar incluye un botón toggle de tema claro/oscuro.
  - **Descripción**: Botón con icono sol/luna en `.va-topbar__theme-toggle`. Alterna `data-theme="light"` y persistencia.
  - **Criterio de aceptación**: El toggle está presente en todas las páginas que usen layout. Cambia el tema sin recargar.
  - **Templates afectados**: `templates/admin/layout.html.twig`, `public/js/admin.js`.
  - **Escenario de prueba**: Pulsar el icono de tema en el topbar cambia inmediatamente los colores de toda la página.

- **REQ-701**: La transición entre temas es suave.
  - **Descripción**: Se aplica `transition` a propiedades de color y fondo controladas por variables para evitar parpadeos bruscos.
  - **Criterio de aceptación**: Al cambiar de tema, fondo, texto, bordes e inputs transicionan en ~200 ms.
  - **Templates afectados**: `public/css/admin/theme.css`, `public/css/admin/root.css`.
  - **Escenario de prueba**: Cambiar de oscuro a claro se percibe como una transición gradual, no un cambio instantáneo brusco.

## Reglas obligatorias

- **REQ-800**: Todas las clases usan prefijo `va-` y nomenclatura BEM.
  - **Descripción**: Ver REQ-001. Bloque-elemento-modificador obligatorio para componentes, variaciones y estados.
  - **Criterio de aceptación**: `grep -R "class=\"[^\"]*s-\|class=\"[^\"]*cyber-" templates/admin/` devuelve 0.
  - **Templates afectados**: Todos.
  - **Escenario de prueba**: Ver REQ-001.

- **REQ-801**: Cada clase CSS se define una sola vez en todo el proyecto (Rule #18).
  - **Descripción**: Ver REQ-004. Cero duplicación de selectores.
  - **Criterio de aceptación**: Ninguna clase `.va-*` aparece más de una vez en `public/css/admin/*.css`.
  - **Templates afectados**: Todos.
  - **Escenario de prueba**: Ver REQ-004.

- **REQ-802**: Cada clase tiene su responsive en una única media query (Rule #17).
  - **Descripción**: Ver REQ-005. Agrupación de breakpoints por clase.
  - **Criterio de aceptación**: No hay dos bloques `@media` separados que modifiquen la misma clase.
  - **Templates afectados**: Todos.
  - **Escenario de prueba**: Ver REQ-005.

- **REQ-803**: Prohibido el uso de atributos `style=""` en los templates HTML.
  - **Descripción**: Toda la presentación vive en archivos `.css`. Cero estilos inline.
  - **Criterio de aceptación**: `grep -R "style=" templates/admin/` devuelve 0 resultados.
  - **Templates afectados**: Todos.
  - **Escenario de prueba**: Inspeccionar cualquier elemento del admin: no tiene atributo `style` en el HTML generado.

- **REQ-804**: Prohibido el uso de manejadores de eventos inline (`onclick`, `onchange`, `onmouseover`, etc.) en templates.
  - **Descripción**: Toda la lógica de eventos vive en `public/js/admin.js` o módulos JS externos. Los templates usan `data-*` o clases para la vinculación.
  - **Criterio de aceptación**: `grep -R "onclick=\|onchange=\|onmouseover=\|onmouseout=" templates/admin/` devuelve 0.
  - **Templates afectados**: Todos, especialmente `templates/admin/dashboard.html.twig`, componentes de Quill, galería y media picker.
  - **Escenario de prueba**: Hacer click en "Publicar" dispara un listener externo; no hay `onclick` en el HTML.
