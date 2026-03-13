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

---

## Agent Team Workflow

### Phase 1 — Design Analysis (run Agent A in foreground)

**Agent A: Design Analyst**

Tasks:
1. Call `get_variable_defs` with the fileKey to retrieve all design tokens (colors, spacing, typography, border)
2. Call `get_design_context` with fileKey + nodeId to get the component spec and auto-generated code hints
3. Call `get_screenshot` to capture the visual reference
4. Analyze the design for interactive elements (filters, toggles, tabs, animations) — these require the Interactivity API

Outputs (pass to Phase 2):
- Complete token list mapped to `theme.json` preset format
- Visual specification (component structure, states, responsive behavior)
- Interactivity requirements: yes/no + list of interactive behaviors
- Suggested block name (kebab-case, e.g. `portfolio-card`)

---

### Phase 2 — Architecture (run Agent B in foreground, uses Phase 1 output)

**Agent B: Block Architect**

Tasks:
1. Define `block.json` — name, title, description, category, icon, keywords
2. Define all block attributes with types, defaults, and sources
3. Decide supports (color, spacing, typography — only what design uses)
4. Confirm whether `viewScriptModule` is needed (if Interactivity API required)
5. Map design tokens to `theme.json` presets (use Phase 1 token list)

Outputs:
- Final `block.json` content
- `theme.json` additions (new presets to add)
- Attribute schema for use in `render.php` and `edit.js`

---

### Phase 3 — Implementation (run Agents C and D in parallel)

**Agent C: PHP + HTML Developer**

Tasks:
1. Create `src/blocks/{block-name}/render.php` — semantic HTML5, BEM classes, WCAG 2.1 AA
2. Use `get_block_wrapper_attributes()` on the root element
3. Pass server-side state via `wp_interactivity_state()` if Interactivity API is used
4. Escape all output (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`)
5. Update `src/Services/BlockRegistrar.php` if needed

Rules to follow (from CLAUDE.md):
- Zero `border-radius`
- Semantic elements (`<article>`, `<section>`, etc.)
- Card link pattern: link on title, `aria-hidden="true"` on image
- `defined('ABSPATH') || exit;` at top of every PHP file

**Agent D: Frontend Developer** (runs in parallel with Agent C)

Tasks:
1. Create `scss/_variables.scss` — map theme.json tokens to SCSS variables using `var(--wp--preset--...)`
2. Create `scss/_block.scss` — BEM styles, mobile-first, no utility classes, no hardcoded values
3. Create `scss/style.scss` — frontend entry, imports _variables + _block
4. Create `scss/editor.scss` — editor-only styles (if needed)
5. Create `view.js` — Interactivity API store (only if Phase 1 identified interactive elements)
6. Create `index.js` + `edit.js` — block registration and editor UI

Rules to follow (from CLAUDE.md):
- BEM prefix: `.wlc-{block-name}`
- All values via `var(--wp--preset--...)` — no hardcoded colors/spacing
- Interactivity API store namespace = `wlc/{block-name}`
- Do not use `data-wp-ignore`

---

### Phase 4 — QA Review (run Agent E in foreground, after C + D complete)

**Agent E: QA / Accessibility Reviewer**

Tasks:
1. Read all generated files
2. Check WCAG 2.1 AA:
   - Alt text on all images
   - Focus indicators on interactive elements
   - ARIA labels where needed
   - Color contrast (flag any color pairs that may be below 4.5:1)
3. Check BEM naming consistency
4. Check PHP: all output escaped, no hardcoded values
5. Check SCSS: no raw hex/px values, correct BEM, mobile-first
6. Check `block.json`: apiVersion 3, correct render/viewScriptModule paths
7. Check Interactivity API: unique IDs, no `data-wp-ignore`, correct namespace

Output:
- List of issues with file + line references, or "All checks passed"
- Fix any issues found directly

---

## File Checklist

After all agents complete, verify these files exist and are non-empty:

```
plugins/wlc-blocks/src/blocks/{block-name}/
├── block.json          ✓
├── index.js            ✓
├── edit.js             ✓
├── render.php          ✓
├── view.js             ✓ (if interactive)
└── scss/
    ├── _variables.scss ✓
    ├── _block.scss     ✓
    ├── style.scss      ✓
    └── editor.scss     ✓
```

Also verify `theme.json` presets have been updated with new tokens.

## Post-build steps

1. Run `npm run build` inside `plugins/wlc-blocks/`
2. In WordPress Playground (http://localhost:9400), activate the plugin
3. Create a new page, add the block, verify it renders correctly
4. Check editor view matches design screenshot from Phase 1
