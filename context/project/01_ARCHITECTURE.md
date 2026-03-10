# 01 Architecture

## Philosophy: Modern Monolith (Server-Driven SPA)

This stack follows the **modern monolith** pattern: a single Laravel application serves both the backend logic and the frontend UI without a separate API layer. Inertia.js bridges the two sides, letting Laravel controllers pass data directly to React page components as typed props.

## Layer Diagram

```
┌─────────────────────────────────────────────────────────┐
│                     BROWSER                             │
│                                                         │
│  ┌────────────────────────┐  ┌───────────────────────┐  │
│  │   React SPA (Inertia)  │  │  Filament Admin Panel │  │
│  │  ┌──────────────────┐  │  │     (Livewire)        │  │
│  │  │ Shadcn / HeroUI  │  │  │                       │  │
│  │  │ Motion animations│  │  │                       │  │
│  │  │ Tailwind 4 CSS   │  │  │                       │  │
│  │  └──────────────────┘  │  │                       │  │
│  └───────────┬────────────┘  └───────────┬───────────┘  │
│              │ XHR (Inertia protocol)    │ Livewire     │
└──────────────┼───────────────────────────┼──────────────┘
               │                           │
┌──────────────┼───────────────────────────┼──────────────┐
│              ▼          LARAVEL          ▼              │
│  ┌────────────────────┐       ┌──────────────────────┐  │
│  │  Web Controllers   │       │  Filament Resources  │  │
│  │  (Inertia::render) │       │  (CRUD, Forms, etc.) │  │
│  └────────┬───────────┘       └──────────┬───────────┘  │
│           │                              │              │
│  ┌────────▼──────────────────────────────▼───────────┐  │
│  │          Actions / Services / Domain Logic        │  │
│  └────────────────────────┬──────────────────────────┘  │
│                           │                             │
│  ┌────────────────────────▼──────────────────────────┐  │
│  │               Eloquent ORM / Database             │  │
│  └───────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
```

## Layer Responsibilities

### Backend (Laravel)

| Layer | Responsibility |
|---|---|
| **Routes** (`routes/web.php`) | Map URLs to controllers; apply middleware |
| **Middleware** | Auth guards, `HandleInertiaRequests` (shared props), CSRF |
| **Controllers** | Receive requests, call Actions/Services, return `Inertia::render()` |
| **Form Requests** | Input validation (server-side, always) |
| **Actions / Services** | Encapsulate business logic in single-responsibility classes |
| **Models** | Eloquent models, relationships, scopes, accessors/mutators |
| **Filament Resources** | Admin CRUD, forms, tables, widgets — self-contained under `app/Filament/` |

### Bridge (Inertia.js)

- **Request cycle**: Browser navigates → Inertia sends XHR → Laravel controller returns `Inertia::render('Page/Name', $props)` → Inertia swaps the React component with new props.
- **Shared data**: The `HandleInertiaRequests` middleware injects global data (auth user, flash messages, etc.) into every response via `share()`.
- **Forms**: Use `useForm()` hook for form state, validation errors, and submission — no manual `fetch`/`axios` calls.
- **SSR**: Optional server-side rendering via `@inertiajs/server` for SEO-critical pages.

### Frontend (React)

| Layer | Responsibility |
|---|---|
| **Pages** (`resources/js/Pages/`) | Route-bound components; receive Inertia props; `export default` |
| **Layouts** (`resources/js/Layouts/`) | Persistent layouts wrapping pages (nav, footer, sidebars) |
| **Components** (`resources/js/Components/`) | Reusable UI blocks; named exports |
| **Components/ui** | Shadcn/ui primitives (Button, Dialog, etc.) — editable source files |
| **lib/** | Hooks, utilities, constants, Motion variant definitions |

### Styling Layers

```
Tailwind 4 (@theme tokens)
  └── Shadcn/ui (Radix primitives + Tailwind classes)
  └── HeroUI (Tailwind-based npm components)
  └── Motion (animation via transform/opacity, no layout-triggering props)
```

- **Tailwind 4** is configured entirely in CSS (`@import "tailwindcss"` + `@theme { ... }`). No `tailwind.config.js`.
- **Shadcn** components live in the project source; you own and edit them.
- **HeroUI** components are imported from `@heroui/*` packages; wrap or extend if customization is needed.
- **Motion** animates via `<motion.div>` etc.; variants are defined outside components to avoid re-creation on each render.

### Admin Panel (Filament)

Filament is a **Livewire-based** admin panel running at `/admin`. It is architecturally separate from the React frontend:

- Uses its own Blade/Livewire views, not React.
- Shares Laravel models, actions, and database with the Inertia app.
- Has its own auth guard (configurable; often `web` with role-based access).
- Resources, Pages, Widgets, and RelationManagers live under `app/Filament/`.

## Build Tooling

| Tool | Purpose |
|---|---|
| **Vite** | Frontend asset bundling, HMR, CSS compilation |
| **Composer** | PHP dependency management |
| **npm / pnpm** | JS dependency management |
| **Laravel Pint** | PHP code formatting (PSR-12 based) |
| **ESLint + Prettier** | JS/TS linting and formatting |
