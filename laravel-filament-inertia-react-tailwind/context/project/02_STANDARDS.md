# 02 Standards

## File & Directory Naming

| Scope | Convention | Example |
|---|---|---|
| PHP classes | PascalCase | `CreateUserAction.php` |
| PHP config/routes | snake_case | `routes/web.php` |
| React page components | PascalCase (match Inertia route) | `Pages/Auth/Login.tsx` |
| React shared components | PascalCase | `Components/UserAvatar.tsx` |
| Shadcn/ui primitives | kebab-case file, PascalCase export | `components/ui/dropdown-menu.tsx` → `DropdownMenu` |
| Directories | kebab-case | `resources/js/lib/` |
| CSS files | kebab-case | `app.css` |
| Test files (PHP) | PascalCase + `Test` suffix | `CreateUserActionTest.php` |
| Test files (JS) | PascalCase + `.test.tsx` | `UserAvatar.test.tsx` |

## Component Export Rules

- **Inertia page components**: Use `export default` (required by Inertia's resolver).
- **All other components**: Use **named exports** for consistent, searchable imports.

```tsx
// ✅ Page component (resources/js/Pages/Dashboard.tsx)
export default function Dashboard({ stats }: DashboardProps) { ... }

// ✅ Shared component (resources/js/Components/StatCard.tsx)
export function StatCard({ title, value }: StatCardProps) { ... }
```

## Import Ordering

### PHP Files

```php
// 1. PHP native classes
use Exception;
// 2. Laravel/framework classes
use Illuminate\Http\Request;
// 3. Third-party packages
use Filament\Resources\Resource;
// 4. App classes (alphabetical)
use App\Actions\CreateUserAction;
use App\Models\User;
```

### TypeScript / JSX Files

```tsx
// 1. React / framework imports
import { useState } from 'react'
// 2. Inertia imports
import { useForm, usePage, Link } from '@inertiajs/react'
// 3. Third-party libraries (Motion, HeroUI, etc.)
import { motion } from 'motion/react'
import { Button } from '@heroui/button'
// 4. Shadcn/ui components
import { Dialog } from '@/components/ui/dialog'
// 5. Project components & utilities
import { StatCard } from '@/Components/StatCard'
import { cn } from '@/lib/utils'
// 6. Types
import type { PageProps } from '@/types'
```

## TypeScript Conventions

- Use **TypeScript** (`.tsx` / `.ts`) for all frontend code.
- Define **prop interfaces** for every component. Suffix with `Props`.
- Prefer `interface` for object shapes; use `type` for unions/intersections.
- Use `@/` path alias for `resources/js/`.

```tsx
interface DashboardProps {
  stats: {
    users: number
    revenue: number
  }
}
```

## Commit Messages

Follow [Conventional Commits](https://www.conventionalcommits.org/):

```
<type>(<scope>): <short description>

[optional body]
[optional footer]
```

| Type | When |
|---|---|
| `feat` | New feature |
| `fix` | Bug fix |
| `refactor` | Code restructure (no behavior change) |
| `style` | Formatting, whitespace |
| `docs` | Documentation only |
| `test` | Adding or fixing tests |
| `chore` | Tooling, deps, config |

Scope examples: `auth`, `dashboard`, `filament`, `ui`, `api`.

### Commit size limits

- **Maximum 150 lines changed per commit.** If a change is larger, split it into logical commits.
- **Refactors go in a separate PR.** Never mix refactoring with feature or bugfix work.

## PR Expectations

Every pull request should include:
1. **Summary** — what changed and why.
2. **Testing** — what was tested (automated + manual).
3. **Screenshots** — if UI changed.
4. **Breaking changes** — if any, called out explicitly.

## Path Boundary Policy

> See **ADR-007** in `context/project/03_DECISIONS.md` for the rationale.

Before modifying any file, check it against the boundary lists below.

### Allowed roots (safe to edit)

| Path | Scope |
|---|---|
| `packages/**` | Plugin / package code |
| `resources/js/**` | React frontend (pages, components, lib) |
| `resources/css/**` | Stylesheets |
| `resources/views/**` | Blade templates (root Inertia template) |
| `context/**` | AI context files (with `[CONTEXT-UPDATE]` prompts) |
| `tests/**` | Test files |

### Forbidden roots (core — ask before touching)

| Path | Why |
|---|---|
| `app/**` | Core Laravel application code |
| `routes/**` | Route definitions |
| `database/**` | Migrations, seeders, factories |
| `config/**` | Framework and package configuration |
| `bootstrap/**` | Framework bootstrap |
| `public/**` | Public assets / entry point |

### Exception process

If a task **cannot be completed** without modifying a forbidden root:
1. **Stop.** Do not write any code in forbidden paths.
2. **State** which forbidden file(s) you need to modify and why.
3. **Wait** for explicit permission before proceeding.

> **Note:** These are template defaults. Adjust the allowed/forbidden lists per project. The invariant is: *the lists exist and agents respect them*.

### Package routes & config

Routes and config **for packages** must live inside the package itself (`packages/<Vendor>/<Package>/routes/`, `packages/<Vendor>/<Package>/config/`). The package's `ServiceProvider` loads them via `loadRoutesFrom()` and `mergeConfigFrom()`. Touching the project's `routes/**` or `config/**` requires explicit permission, **even when working on a package**.
