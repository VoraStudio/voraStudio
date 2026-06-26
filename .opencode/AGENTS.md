# Normas del workspace — VoraStudio

## Normas globales

1. **Propiedades Personalizadas de CSS para todo:** Usa variables de CSS (:root) para cada color, tamaño de fuente, valor de espaciado, radio de borde, velocidad de transición e índice Z. Nada debe estar escrito directamente en los selectores. Cada decisión de diseño vive en :root para poder cambiarse en un solo lugar.
2. **Tipografía fluida con clamp():** Todos los valores de font-size deben usar CSS clamp(). Formato: `clamp(mínimo(rem), calc(preferido(vw) + preferido(rem)), máximo(rem))`. Ejemplo: `clamp(1rem, calc(2.5vw + 1rem), 1.5rem)`. Nunca uses tamaños de fuente en píxeles fijos. Esto hace que el texto escale perfectamente sin media queries.
3. **Sistema de diseño CSS Grid + Flexbox:** Usa CSS Grid para los diseños de página y sección. Usa Flexbox para la alineación de componentes (centrar elementos en una card, ítems de navegación). Nunca uses floats, tablas para maquetar ni posicionamiento absoluto para elementos de flujo.
4. **Rendimiento de imágenes (Lazy loading + Dimensiones):** Cada img debe tener atributos width y height explícitos. Las imágenes fuera del primer pantallazo deben usar loading="lazy". Esto evita el Cumulative Layout Shift (CLS) y acelera la carga inicial.
5. **Escala de espaciado de 8px:** Todos los valores de margen, padding y gap deben ser múltiplos de 8px (8, 16, 24, 32...). Nunca uses valores arbitrarios como 13px o 22px para mantener la consistencia visual.
6. **Contraste mínimo WCAG AA:** Cada texto debe mantener un ratio de contraste mínimo de 4.5:1 con su fondo. Verifica matemáticamente los colores, especialmente en fondos oscuros o textos secundarios.
7. **Microinteracciones en elementos interactivos:** Botones, enlaces e inputs deben tener estados hover, active y focus deliberados. Usa transiciones CSS de 200-300ms con cubic-bezier(0.4, 0, 0.2, 1).
8. **HTML5 semántico con etiquetas ARIA:** Usa header, nav, main, section, article, aside y footer correctamente. Añade aria-label a botones de iconos y elementos sin texto visible.
9. **Uso de will-change:** Aplica will-change: transform antes de animar y elimínalo al finalizar con el evento transitionend. Esto fuerza la creación de capas en la GPU para animaciones fluidas sin repintados.
10. **Diseño Responsivo Mobile-first:** Escribe los estilos base para móvil primero y usa media queries de min-width para escalar. Usa @container queries cuando sea posible y prueba en 320px, 375px, 768px, 1024px y 1440px.
11. **Toda instrucción sin comentarios**, excepto mediaQueries o lógica muy compleja.
12. **Separación de secciones en html, css y js:** Mediante separadores claros.
13. **Uso y conocimiento de plugin gsap especiales:** Los plugins de Gsap son gratuitos queda PROHIBIDO informar de que son de pagos y también aclarar, que el uso de la propiedad mask, de splitText es 100% viable.
14. **Cada instrucción debe ser revisada antes de dar el resultado** para asegurar que no se cometen errores o se omiten partes de código importante.
15. **A cada nueva instrucción se debe indicar SIEMPRE la skill que se hace uso.**

## Normas específicas del proyecto

16. **TODA SECCIÓN debe ser responsive obligatoriamente:** Cada sección, panel, tabla, formulario y componente del admin debe adaptarse correctamente a cualquier tamaño de pantalla. No se aceptan paneles que se rompan en móvil. Esto incluye:
    - Tablas con scroll horizontal en viewports pequeños
    - Botones y action bars que no se solapen
    - Formularios con inputs full-width en móvil
    - Cards y grids que se recolumen
    - Sidebar que se oculta con toggle
    - Dashboard que se adapta a una columna

## Idioma

- Responder siempre en español

## Stack

- Vanilla HTML, CSS y JavaScript (sin frameworks)
- Symfony 7 (VoraCMS backend)

## Convenciones de código

- Usar BEM para nombrar clases CSS
- JavaScript ES6+ (modules, const/let, arrow functions)
- Código limpio, sin comentarios innecesarios
- No emojis en el código
