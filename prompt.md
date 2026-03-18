Stwórz zespół agentów do zbudowania bloku Gutenberg.

## Design
Figma: https://www.figma.com/design/G8VhXEbUB6uesj97l7knxv/WLC-Portfolio?node-id=4-3&t=wvje4UmPypzpEi4u-4

## Blok
- Nazwa: `wlc/portfolio-grid`
- Tytuł: "WLC Portfolio Grid"
- Kategoria: `widgets`

## Custom Post Type
- Slug: `wlc-portfolio`
- Pola: title, editor, thumbnail, excerpt
- Rewrite slug: `portfolio`

## Taksonomie (filtry w bloku)
| Taksonomia | Slug | Typ | Hierarchia |
|---|---|---|---|
| Kategoria | `portfolio-category` | hierarchical | tak |
| Technologia | `technology` | non-hierarchical | nie |

## Zachowanie frontendu
- Siatka kart: 1 kol. mobile → 2 kol. tablet (≥640px) → 3 kol. desktop (≥1024px)
- Filtrowanie Interactivity API: AND logic (kategoria AND technologia)
- Paginacja po stronie klienta
- Fallback dla braku JS: link do archiwum `/portfolio`
- Liczba kart na stronę: konfigurowalna w edytorze (domyślnie 9)

## Wymagania edytora
- InspectorControls z:
  - NumberControl: "Kart na stronę" (postsPerPage, min 1, max 50, default 9)
  - CheckboxControl dla każdej taksonomii: "Pokaż filtr Kategoria", "Pokaż filtr Technologia"
- Podgląd siatki: pobierz max 3 posty z REST API, wyrenderuj karty z prawdziwym HTML
- Placeholder gdy brak postów: "Dodaj wpisy portfolio żeby zobaczyć podgląd"

Na koniec wygeneruj plik .xml z 12 przykładowymi postami do zaimportowania w WordPress.