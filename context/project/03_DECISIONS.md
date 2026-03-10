# 03 Decisions (ADR Log)

> **This file is the source of truth.** If anything in the context files conflicts, what's written here wins.

---

## ADR-001: Inertia.js over REST / GraphQL API

**Status:** Accepted

**Context:** The app needs a dynamic SPA experience but doesn't require a public API or mobile clients (initially).

**Decision:** Use Inertia.js to bridge Laravel controllers and React pages. No REST or GraphQL API layer.

**Consequences:**
- Controllers return `Inertia::render()` instead of JSON — simpler data flow.
- No need to maintain API versioning, serialization, or separate auth for API consumers.
- If a public API is needed later, it can be added alongside Inertia without conflict.
- Frontend routing is server-driven (Laravel owns the routes).

---

## ADR-002: Filament for Admin Panel (Livewire-based, separate from React)

**Status:** Accepted

**Context:** The app needs an admin panel for CRUD operations, user management, and settings.

**Decision:** Use Filament 3+ as a self-contained admin panel under `/admin`, powered by Livewire.

**Consequences:**
- Admin panel uses Livewire components (Blade), not React — two rendering paradigms coexist.
- Filament and the Inertia frontend share Laravel models, actions, and the database.
- Admin routes, views, and assets are isolated — no tight coupling with the React frontend.
- Filament plugins (Shield, Breezy, etc.) work out of the box.

---

## ADR-003: Tailwind CSS 4 with CSS-First Configuration

**Status:** Accepted

**Context:** Tailwind 4 introduces a CSS-first configuration model, replacing the JS-based `tailwind.config.js`.

**Decision:** Use Tailwind 4's native `@theme` directive for all customization. No `tailwind.config.js` unless a third-party plugin explicitly requires `@config`.

**Consequences:**
- CSS entry point (`resources/css/app.css`) contains `@import "tailwindcss"` + `@theme { ... }` block.
- Design tokens (colors, spacing, fonts) are defined as CSS custom properties inside `@theme`.
- PostCSS config uses `@tailwindcss/postcss` only — no `autoprefixer` or `postcss-import` needed.
- HeroUI and Shadcn paths must be included in Tailwind's automatic content detection or explicitly configured.

---

## ADR-004: Shadcn/ui as Primary + HeroUI as Complement

**Status:** Accepted

**Context:** The frontend needs accessible, customizable UI components and some design-polished elements.

**Decision:**
- **Shadcn/ui** is the primary component source — primitives are copied into `resources/js/components/ui/` and are fully editable.
- **HeroUI** is used for complementary components not covered by Shadcn or where HeroUI's design is preferred.

**Consequences:**
- Shadcn components are project-owned source code — customize freely.
- HeroUI components are npm dependencies — wrap or extend, don't fork.
- Never use both libraries for the **same component type** (e.g., don't mix a Shadcn Button with a HeroUI Button in the same project).
- Both libraries share Tailwind for styling — minimal conflict risk.
- Watch for naming collisions when importing; use explicit aliased imports if needed.

---

## ADR-005: Motion (ex-Framer Motion) for Animations

**Status:** Accepted

**Context:** The frontend needs smooth, declarative animations for page transitions, micro-interactions, and layout changes.

**Decision:** Use Motion (the renamed Framer Motion library, `motion/react`) for all animations.

**Consequences:**
- Import from `motion/react` (not the deprecated `framer-motion` package).
- Use `<motion.div>` and friends for animated elements.
- Define animation variants as constants **outside** components to avoid re-creation.
- Animate only GPU-friendly properties (`transform` props: `x`, `y`, `scale`, `rotate`; and `opacity`). Never animate `width`, `height`, `top`, `left`, `padding`, or `margin`.
- Use `<AnimatePresence>` for exit animations; always provide a unique `key`.
- Use `<LazyMotion features={domAnimation}>` to reduce bundle size when full gesture support isn't needed.

---

## ADR-006: Conflict Resolution — This File Wins

**Status:** Accepted

**Decision:** If any context file, style guide, or architecture document contradicts this decisions file, **this file takes precedence**. This ensures a clear, unambiguous source of truth for AI agents and human developers alike.

---

## ADR-007: Path Boundary Policy (Allowed vs Forbidden Roots)

**Status:** Accepted

**Context:** AI agents can "escape" their intended scope and modify core application files when the task only requires working in plugins, frontend, or context files. Without an explicit boundary, the non-negotiable constraint "minimal, scoped changes only" is unenforceable.

**Decision:** Maintain an explicit **allowed roots** / **forbidden roots** list in `context/project/02_STANDARDS.md`. Agents must check every file path against these lists before writing code. If a task requires modifying a forbidden root, the agent must **stop and request explicit permission**.

**Consequences:**
- The operational checklist (which paths, do/don't) lives in `02_STANDARDS.md § Path Boundary Policy`.
- Each project customizes the lists — the invariant is that the lists exist, not their exact contents.
- Agents that respect this policy will never accidentally refactor core code during a plugin task.
- The "exception process" (stop → state why → wait for permission) prevents silent scope creep.

---

## ADR-008: QA as a Project-Level Invariant

**Status:** Accepted

**Context:** AI agents and human developers can each independently judge code as "done" without running the quality gate. This creates inconsistency: code that looks correct may still fail linting, type checks, tests, or the production build. The source of truth must be the pipeline, not the author's confidence.

**Decision:** The QA pipeline is a **non-negotiable delivery gate**. A task is considered done only when the full pipeline passes (or every deviation is explicitly documented with the exact reason).

**Canonical validation entrypoints** (preferred — when available in `composer.json`):

| Script | Purpose |
|---|---|
| `composer qa:fix` | Safe auto-fixes only (Pint + ESLint `--fix`) — run first |
| `composer qa:quick` | Fast local validation (lint + typecheck + tests) |
| `composer qa` | Full delivery gate — all 7 steps in order |

If these canonical scripts are **not yet defined** in the project, agents must use the ordered fallback command chain from `04_RUNBOOK.md § Quality Gate Pipeline` and **explicitly report the deviation** in the delivery output.

**Consequences:**
- "Task complete" is defined by pipeline results, not by agent or developer confidence.
- QA belongs to the project, not to any specific AI tool — agents and humans use the same commands.
- Any suppression of lint, type, or test errors requires an inline comment with a documented reason. Silent suppression is treated as broken code.
- If a command cannot be executed because the environment lacks support, this must be reported explicitly — not silently skipped.
- If a canonical script is missing from the project, this must be reported explicitly and the fallback chain used instead.
