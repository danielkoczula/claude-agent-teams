# Zatrudniłem zespół agentów AI, żeby klepali za mnie bloki

Demo repo na prezentację live-codingową na meetup WLC.

Pokazujemy jak używać **Claude Code z Agent Teams** do budowania bloków Gutenberga — na przykładzie bloku `WLC Portfolio Grid`: siatka kart portfolio z filtrami kategorii i technologii, działającymi przez Interactivity API.

## Co tu jest

```
plugins/wlc-blocks/   # Plugin WordPress z blokiem
blueprint.json        # Konfiguracja WordPress Playground
package.json          # Jeden skrypt: npm start
CLAUDE.md             # Instrukcje dla agentów AI
```

## Jak odpalić WordPress

Wymagania: Node.js

```bash
npm start
```

Otwiera WordPress Playground pod adresem `http://localhost:9400` (lub innym wolnym portem).

Login: `admin` / Hasło: `admin`

Plugin `wlc-blocks` jest automatycznie zamontowany z folderu `./plugins/` — po zbudowaniu pluginu aktywuj go ręcznie w WP Admin.

## Budowanie pluginu

```bash
cd plugins/wlc-blocks
npm install
composer install
npm run build   # jednorazowy build
npm run start   # tryb watch (dev)
```


tmux -CC

claude --teammate-mode tmux