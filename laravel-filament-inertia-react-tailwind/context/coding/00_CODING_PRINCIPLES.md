# Coding Principles (reusable)

## Universal

- **Clarity over cleverness.** Write code a junior developer can read without comments.
- **Keep diffs small and scoped.** One concern per commit; no unrelated refactors.
- **Don't change behavior unless requested.** Preserve existing contracts.
- **Add or adjust tests for every behavior change.**

## Backend (Laravel)

- **Lean controllers.** Controllers receive, validate, delegate, and respond — nothing more. Business logic belongs in Action or Service classes.
- **Always use Form Requests** for input validation. Never validate inline in controllers.
- **Explicit over magic.** Prefer explicit Eloquent queries over implicit global scopes when clarity matters.
- **Fail loudly.** Throw specific exceptions; let the exception handler render the right response.

## Frontend (React + Inertia)

- **Props are the source of truth.** Server data arrives as Inertia props — don't duplicate it into local state (`useState`) unless the component needs to mutate it locally.
- **Component composition over prop drilling.** Use React Context or slot patterns instead of passing props through 5+ levels.
- **Explicit prop typing.** Every component must have a typed `Props` interface — no `any`, no implicit props.
- **Named exports** for all components except Inertia pages (which require `export default`).

## Styling (Tailwind 4 + Shadcn + HeroUI)

- **Utility-first.** Use Tailwind classes directly; avoid custom CSS unless truly needed.
- **`@theme` for tokens.** All design tokens (colors, spacing, fonts) go in the `@theme { }` block in `app.css`. Never create a `tailwind.config.js` unless forced by a plugin.
- **Don't mix component sources** for the same element type. Pick Shadcn or HeroUI for each component type and be consistent.
- **Use `cn()` utility** (from `lib/utils.ts`) for conditional/merged class names.

## Animations (Motion)

- **Animate only GPU-friendly properties.** Stick to `transform` props (`x`, `y`, `scale`, `rotate`) and `opacity`. Never animate `width`, `height`, `top`, `left`, `padding`, `margin`, or `border-width`.
- **Define variants outside components.** Animation variant objects must be module-level constants, not inline objects that get recreated on every render.
- **Use `LazyMotion`** to reduce bundle size when full gesture support isn't needed.
- **Respect `prefers-reduced-motion`.** Always provide a reduced/no-motion fallback.

## Quality & Verification

- **The pipeline is the final arbiter.** If code passes the full QA pipeline (lint → typecheck → tests → build), it is shippable. If it doesn't, it's not — regardless of how correct it looks.
- **Fix errors at the source.** Never silence a linter, type checker, or test to make the pipeline green. Fix the underlying issue.
- **Type safety is not optional.** No `any`, no `@ts-ignore`, no `@phpstan-ignore` without a documented reason on the same line explaining why the exception is safe.
- **Tests must be meaningful.** A test that always passes regardless of the implementation is worse than no test — it creates false confidence.
- **Code quality is a shared responsibility.** QA belongs to the project, not to any specific tool or workflow. Agents and humans use the same pipeline.
