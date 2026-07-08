# Design: Entry Preview Genérico

## Resolució de templates

```
EntryController::preview()
    │
    ├── $loader->exists("preview_{slug}.html.twig")? ──→ Renderitzar específic
    │
    ├── slug === "vorastudio-projects"? ──→ Renderitzar preview.html.twig (legacy)
    │
    └── else ──→ Renderitzar preview_generic.html.twig
```

## preview_generic.html.twig

### Estructura
```
┌─ preview-container (max-width: 900px, centrat) ─────────────────┐
│                                                                  │
│  ┌─ preview-header ──────────────────────────────────────────┐  │
│  │  Content Type Name (accent color)                         │  │
│  │  Title (data.titol o fallback al nom + ID)                │  │
│  │  Meta: locale, status, published date                     │  │
│  └───────────────────────────────────────────────────────────┘  │
│                                                                  │
│  ┌─ field-card (per cada camp) ───────────────────────────────┐  │
│  │  Field Label (uppercase, muted)                            │  │
│  │  ┌─ field-value ────────────────────────────────────────┐  │  │
│  │  │  • text/textare/richtext → nl2br                     │  │  │
│  │  │  • image              → <img>                        │  │  │
│  │  │  • gallery            → grid de <img>                │  │  │
│  │  │  • youtube            → iframe embed                 │  │  │
│  │  │  • boolean            → badge Sí/No                  │  │  │
│  │  │  • number             → number_format                 │  │  │
│  │  │  • date/datetime      → date filter                  │  │  │
│  │  │  • url                → link                         │  │  │
│  │  │  • email              → mailto                       │  │  │
│  │  │  • color              → swatch + hex                 │  │  │
│  │  │  • location           → text                         │  │  │
│  │  │  • fallback           → json_encode o raw            │  │  │
│  │  └──────────────────────────────────────────────────────┘  │  │
│  └───────────────────────────────────────────────────────────┘  │
│                                                                  │
│  ┌─ preview-footer ──────────────────────────────────────────┐  │
│  │  ← Tancar previsualització                                │  │
│  └───────────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────────────┘
```

### Fonts
- `Inter` per a text general
- `Outfit` per a títols (coherència amb la marca VoraStudio)

### Estil
- Cards blanques amb ombra suau sobre fons gris clar
- Border-radius: 12px
- Accent color: `#f5a04e` (taronja VoraStudio)
- Sense dependències externes (CSS inline al template)
