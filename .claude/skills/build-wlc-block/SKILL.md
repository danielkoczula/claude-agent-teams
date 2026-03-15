---
name: build-wlc-block
description: Builds a WordPress Gutenberg block from a Figma design using agent teams. Fetches design tokens and visual spec from Figma MCP, then orchestrates parallel agents to implement the block following WLC coding standards.
triggers:
  - "build block from figma"
  - "zbuduj blok z figmy"
  - "implement figma block"
  - "create gutenberg block"
  - "/build-wlc-block"
---

# Skill: build-wlc-block

## Purpose

Orchestrate a team of Claude agents to transform a Figma design into a fully working WordPress Gutenberg block inside `plugins/wlc-blocks/`, following all standards defined in `CLAUDE.md`.

## Prerequisites

- Figma MCP server connected (provides `get_design_context`, `get_variable_defs`, `get_screenshot`)
- WordPress Playground running (`npm start` from project root)
- Plugin directory exists: `plugins/wlc-blocks/`

## Required input

Ask the user for a Figma URL before starting. Extract:
- `fileKey` from the URL
- `nodeId` (convert `-` to `:` in node IDs from URL query params)

## MANDATORY: Agent Team Setup (do this BEFORE writing any files)

**Always use a proper agent team with split terminal panes.** Never use background agents or write files directly in the lead session.

### Step 1 — Fetch design in parallel
Call `get_design_context` and `get_variable_defs` simultaneously.

### Step 2 — Create the team
```
TeamCreate(team_name: "portfolio-grid-block", description: "Build wlc/block-name block")
```
This creates the team + shared task list. Teammates will open in split tmux panes automatically (configured in `.claude/settings.json`).

### Step 3 — Create tasks
Use `TaskCreate` for each parallel workstream (one task per agent):
- Task 1: PHP backend (plugin bootstrap, CPT, taxonomies, controller, render.php)
- Task 2: JS frontend (block.json, package.json, index.js, edit.js, view.js)
- Task 3: CSS + config (style.scss, editor.scss, theme/theme.json)

### Step 4 — Spawn 3 agents IN A SINGLE MESSAGE (parallel)
Send all three `Agent` calls in the same response. Each agent **must** include:
- `team_name: "portfolio-grid-block"` — joins the team and gets a tmux pane
- `name: "php-agent"` / `"js-agent"` / `"css-agent"` — addressable by name
- Full design spec in the prompt (conversation history is NOT shared with teammates)

```
Agent(name: "php-agent",  team_name: "portfolio-grid-block", subagent_type: "general-purpose", ...)
Agent(name: "js-agent",   team_name: "portfolio-grid-block", subagent_type: "general-purpose", ...)
Agent(name: "css-agent",  team_name: "portfolio-grid-block", subagent_type: "general-purpose", ...)
```

### Step 5 — Wait and collect
Wait for all 3 teammates to report completion via messages. Then:
1. Send `shutdown_request` to each teammate (individually — cannot broadcast structured messages)
2. Run `composer install && npm install && npm run build` in `plugins/wlc-blocks/`

## File Checklist

After all agents complete, verify these files exist and are non-empty:

```
plugins/wlc-blocks/
├── resources/blocks/{block-name}/
│   ├── block.json          ✓
│   ├── index.js            ✓  (imports ./style.scss)
│   ├── edit.js             ✓  (live preview + InspectorControls)
│   ├── render.php          ✓  (template only, calls controller)
│   ├── style.scss          ✓  (all BEM styles, var() directly)
│   ├── editor.scss         ✓  (@import './style' + overrides)
│   └── view.js             ✓  (if interactive)
└── src/Services/Blocks/
    └── {BlockName}Controller.php  ✓  (with docblocks)
```

Also verify `theme/theme.json` presets have been updated with new tokens (font sizes in rem).

## Pre-build verification

Before building, verify `plugins/wlc-blocks/package.json` scripts include `--experimental-modules` in **both** `start` and `build` commands. This flag is required for `viewScriptModule` (`view.js`) to compile as an ES module.

## Post-build steps

1. Run `npm run build` inside `plugins/wlc-blocks/`
2. In WordPress Playground (http://localhost:9400), activate the plugin
3. Create a new page, add the block, verify it renders correctly
4. Check editor view: confirm live preview loads, InspectorControls are visible, Placeholder shows when no posts

## CSS output filenames

`@wordpress/scripts` produces these filenames — always use them in `block.json`:
- `"editorStyle": "file:./index.css"` — compiled from `editor.scss` via `index.js`
- `"style": "file:./style-index.css"` — compiled from `style.scss`
