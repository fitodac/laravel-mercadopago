# 00 Project Context

## Purpose

This repository is a **reusable AI-context bootstrap** for any project built with the stack below. It provides structured context files that AI coding agents (Codex, Antigravity, Trae, Cursor, etc.) consume automatically to understand the project's architecture, conventions, and constraints before writing code.

> Copy this scaffold into a new project, adjust project-specific details, and every AI agent will start with the same shared understanding.

## Stack Manifest

| Technology | Role | Version Constraint |
|---|---|---|
| **Laravel** | Backend framework (routing, ORM, auth, queues) | 11+ |
| **Filament** | Admin panel (CRUD, forms, tables, widgets) | 3+ |
| **Inertia.js** | Server-driven SPA bridge (no REST API needed) | 2+ |
| **React** | Frontend UI library | 18 / 19 |
| **Tailwind CSS** | Utility-first CSS framework (CSS-first config) | 4+ |
| **Shadcn/ui** | Accessible component primitives (Radix-based, copied into project) | latest |
| **HeroUI** | Complementary component library (npm package, ex-NextUI) | latest |
| **Motion** | Animation library (ex-Framer Motion) | latest |

## Stack Roles Summary

- **Laravel** owns routing, middleware, authentication, authorization, database (Eloquent ORM), validation (Form Requests), queues, and mail. It renders Inertia responses instead of Blade views for the user-facing app.
- **Filament** is a **separate admin surface** mounted at `/admin`, powered by Livewire. It does not share React components with the frontend.
- **Inertia.js** replaces the traditional API layer. Laravel controllers return `Inertia::render()` responses; Inertia hydrates React page components with server-provided props.
- **React** renders the entire user-facing frontend as an SPA. Pages receive typed props from Inertia; shared data flows through the `HandleInertiaRequests` middleware.
- **Tailwind 4** provides styling via a CSS-first configuration (`@theme` directive, no `tailwind.config.js`). Vite compiles the CSS.
- **Shadcn/ui** supplies accessible, customizable primitives (Dialog, Dropdown, Tabs, etc.) that live **inside the project** at `resources/js/components/ui/`.
- **HeroUI** complements Shadcn with additional design-polished components installed as npm packages.
- **Motion** handles all animations declaratively via `<motion.div>` components with `initial`, `animate`, `exit`, and `transition` props.

## Package Managers

| Scope | Manager | Lock File |
|---|---|---|
| PHP dependencies | Composer | `composer.lock` |
| JS dependencies | npm _or_ pnpm | `package-lock.json` / `pnpm-lock.yaml` |

## Key Directories (typical Laravel + Inertia layout)

```
app/                  # Laravel application code
├── Http/Controllers/ # Inertia-aware controllers
├── Models/           # Eloquent models
├── Actions/          # Single-responsibility action classes
├── Filament/         # Filament resources, pages, widgets
resources/
├── js/
│   ├── Pages/        # Inertia page components (PascalCase)
│   ├── Layouts/      # Layout components
│   ├── Components/   # Shared React components
│   │   └── ui/       # Shadcn/ui primitives
│   ├── lib/          # Utilities, hooks, helpers
│   └── app.tsx       # Inertia app bootstrap
├── css/
│   └── app.css       # Tailwind 4 entry (with @theme)
routes/
├── web.php           # Inertia routes
├── admin.php         # Filament routes (auto-registered)
```
