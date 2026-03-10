# 04 Runbook

## Prerequisites

| Dependency | Version | Check |
|---|---|---|
| PHP | 8.2+ | `php -v` |
| Composer | 2+ | `composer --version` |
| Node.js | 20+ | `node -v` |
| npm _or_ pnpm | latest | `npm -v` / `pnpm -v` |
| MySQL / PostgreSQL / SQLite | any supported | `mysql --version` / `psql --version` |

## Initial Setup

```bash
# 1. Install PHP dependencies
composer install

# 2. Install JS dependencies
npm install          # or: pnpm install

# 3. Environment config
cp .env.example .env
php artisan key:generate

# 4. Configure database in .env, then:
php artisan migrate

# 5. (Optional) Seed database
php artisan db:seed
```

## Filament Admin Setup

```bash
# Install Filament panels (if not already installed)
php artisan filament:install --panels

# Create an admin user
php artisan make:filament-user
```

Access the admin panel at: `http://localhost:8000/admin`

## Development Servers

Run **both** commands in separate terminals (or use a process manager like `concurrently`):

```bash
# Terminal 1 — Laravel backend
php artisan serve
# → http://localhost:8000

# Terminal 2 — Vite dev server (HMR)
npm run dev
# → http://localhost:5173 (proxied through Laravel)
```

## Useful Artisan Commands

| Command | Purpose |
|---|---|
| `php artisan make:model Name -mfc` | Create model + migration + factory + controller |
| `php artisan make:filament-resource Name` | Create Filament resource (CRUD) |
| `php artisan make:action Name` | Create an action class |
| `php artisan migrate:fresh --seed` | Reset DB and re-seed |
| `php artisan route:list` | List all registered routes |
| `php artisan tinker` | Interactive REPL |
| `php artisan optimize:clear` | Clear all caches |

## Adding Shadcn Components

```bash
# Add a specific component (e.g., Button)
npx shadcn@latest add button

# Components are installed to: resources/js/components/ui/
```

## Adding HeroUI Components

```bash
# Install a specific component
npm install @heroui/button @heroui/card
# Import directly in your React components
```

## Build for Production

```bash
npm run build        # Compile frontend assets
php artisan optimize # Cache Laravel config, routes, views
```

## Quality Gate Pipeline

> **This section is the canonical QA runbook for all agents and developers.**
> See `context/project/03_DECISIONS.md § ADR-008` for the policy that makes this pipeline mandatory.

---

### Canonical pipeline levels

Prefer these `composer` scripts when they are available in the project:

| Script | What it does | Use when |
|---|---|---|
| `composer qa:fix` | Auto-fixes only — Pint + ESLint `--fix` | Before committing, safe to run anytime |
| `composer qa:quick` | Lint + typecheck + tests (no build) | Fast local feedback loop |
| `composer qa` | Full delivery gate — all 7 steps in order | Before marking any task done |

If these scripts are **not yet defined** in the project, use the fallback command chain below and document the deviation explicitly in your delivery report.

---

### Fallback command chain (7-step pipeline)

Run in strict order. **Stop on failure. Fix the error. Re-run from step 1.**

```bash
# ── Step 1: PHP formatting ───────────────────────────────────
./vendor/bin/pint --test
# Fails on any diff. Auto-fix with: ./vendor/bin/pint

# ── Step 2: PHP static analysis ─────────────────────────────
./vendor/bin/phpstan analyse --memory-limit=256M
# Skip if larastan is not installed — document the skip.

# ── Step 3: JS/TS linting ────────────────────────────────────
npm run lint
# Auto-fix with: npm run lint:fix (or: npm run lint -- --fix)
# Skip if the lint script is not defined — document the skip.

# ── Step 4: TypeScript type check ────────────────────────────
npx tsc --noEmit
# Skip if tsconfig.json is not present — document the skip.

# ── Step 5: PHP tests ─────────────────────────────────────────
php artisan test --parallel
# Falls back to: php artisan test (without --parallel)

# ── Step 6: JS/TS tests ───────────────────────────────────────
npm run test
# Skip if the test script is not defined — document the skip.

# ── Step 7: Production build ─────────────────────────────────
npm run build
# No exceptions — build failure is always a blocking error.
```

---

### Graceful-skip rule

A check may be skipped if and only if it is **not installed or not configured** in the project. The skip must be:
- **Explicit** — listed in the delivery report with the step name and reason.
- **Never silent** — omitting a step from the report is not the same as skipping it.

Do not install or configure missing tools unless explicitly asked.

---

### Error-handling protocol

| Failure | Allowed fix | Not allowed |
|---|---|---|
| Pint `--test` fails | Run `./vendor/bin/pint` (auto-fix), then re-run `--test` | Suppressing or ignoring diffs |
| PHPStan error | Fix the type issue | `@phpstan-ignore` without a documented reason on the same line |
| ESLint error | Run `lint:fix`, then re-run `lint` | Disabling rules without a documented reason |
| TypeScript error | Fix the type | `as any` or `@ts-ignore` without a documented reason on the same line |
| Test failure | Fix the code or update the test (if behavior change is intentional — document why) | Commenting out or skipping failing tests silently |
| Build failure | **Blocking error — no exceptions.** Fix before delivering. | Delivering without a passing build |

---

### Delivery report (required)

Every agent must produce this short report at the end of a task:

```
## Delivery Report

**Summary:** [One-line description of what was implemented]

**Files touched:**
- path/to/file.php
- path/to/Component.tsx

**Pipeline executed:**
| Step | Command | Result |
|---|---|---|
| 1. PHP lint     | ./vendor/bin/pint --test         | ✅ Pass / ❌ Fail / ⏭ Skip (reason) |
| 2. PHPStan      | ./vendor/bin/phpstan analyse     | ✅ / ❌ / ⏭ |
| 3. JS lint      | npm run lint                     | ✅ / ❌ / ⏭ |
| 4. Typecheck    | npx tsc --noEmit                 | ✅ / ❌ / ⏭ |
| 5. PHP tests    | php artisan test --parallel      | ✅ / ❌ / ⏭ |
| 6. JS tests     | npm run test                     | ✅ / ❌ / ⏭ |
| 7. Build        | npm run build                    | ✅ / ❌ / ⏭ |

**Blockers / deviations:** [None | description of any skipped step or unresolved issue]
```

If the environment cannot execute commands (read-only context, sandboxed agent), provide the exact commands above, request the output, and use the output to fix issues before claiming done.



## Common Troubleshooting

| Issue | Solution |
|---|---|
| Vite HMR not working | Ensure `npm run dev` is running; check `vite.config.ts` server settings |
| Inertia page not found | Verify the component path in `Inertia::render()` matches `resources/js/Pages/...` |
| Tailwind classes not applied | Ensure the file path is covered by Tailwind's content detection (check `resources/js/**/*.tsx`) |
| Filament styles missing | Run `php artisan filament:assets` |
| CSRF token mismatch | Ensure `@inertiaHead` is in your root Blade template |
| Motion animations janky | Animate only `transform`/`opacity`; avoid layout-triggering properties |
| `pint --test` fails | Run `./vendor/bin/pint` to auto-fix, then re-check |
| `tsc --noEmit` errors | Fix type errors before running tests — types catch bugs tests miss |
