# Testing Strategy (reusable)

## Testing Layers

| Layer | Tool | Runs via | What to Test |
|---|---|---|---|
| PHP Unit | Pest PHP | `php artisan test` | Actions, Services, Models, utility functions |
| PHP Feature | Pest PHP | `php artisan test` | Controllers, middleware, Inertia responses, auth flows |
| Filament | Pest + Filament testing helpers | `php artisan test` | Resource pages, form validation, table filters |
| React Component | Vitest + React Testing Library | `npm run test` | Component rendering, user interactions, accessibility |
| E2E (optional) | Playwright _or_ Laravel Dusk | `npx playwright test` / `php artisan dusk` | Critical user journeys end-to-end |

## PHP Testing (Pest)

### Unit Tests

Test isolated logic — Actions, Services, value objects, model methods:

```php
it('creates a user with valid data', function () {
    $action = new CreateUserAction();
    $user = $action->execute(new CreateUserData(
        name: 'Jane Doe',
        email: 'jane@example.com',
    ));

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('Jane Doe');
});
```

### Feature Tests

Test the full HTTP cycle, including Inertia rendering:

```php
it('renders the dashboard page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('stats')
        );
});
```

### Filament Tests

Use Filament's built-in `livewire()` test helpers:

```php
it('can list users in the admin panel', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(ListUsers::class)
        ->assertCanSeeTableRecords(User::all());
});
```

### Conventions

- Test files mirror the app structure: `tests/Unit/Actions/CreateUserActionTest.php`.
- Use Pest's `describe` / `it` syntax (not PHPUnit's class-based style).
- Factories for all models — never create test data with raw inserts.
- Use `RefreshDatabase` trait for feature tests.

## Frontend Testing (Vitest + React Testing Library)

### Component Tests

Test rendering and user interaction:

```tsx
import { render, screen } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { StatCard } from '@/Components/StatCard'

describe('StatCard', () => {
  it('renders the title and value', () => {
    render(<StatCard title="Users" value={42} />)

    expect(screen.getByText('Users')).toBeInTheDocument()
    expect(screen.getByText('42')).toBeInTheDocument()
  })

  it('fires onClick when clicked', async () => {
    const onClick = vi.fn()
    render(<StatCard title="Users" value={42} onClick={onClick} />)

    await userEvent.click(screen.getByRole('button'))
    expect(onClick).toHaveBeenCalledOnce()
  })
})
```

### Conventions

- Test files co-located: `Components/StatCard.test.tsx` next to `Components/StatCard.tsx`.
- Use `screen` queries (not destructured from `render`) for readability.
- Prefer `getByRole` / `getByLabelText` over `getByTestId` for accessibility-first testing.
- Mock Inertia's `usePage` and `useForm` when testing page components.

## What to Test at Each Layer

| Concern | Where to Test |
|---|---|
| Validation rules | PHP Feature test (Form Request) |
| Business logic | PHP Unit test (Action/Service) |
| Auth / authorization | PHP Feature test (middleware, gates) |
| Inertia page renders | PHP Feature test (`assertInertia`) |
| Component behavior | Vitest + RTL |
| Full user flow | E2E (Playwright/Dusk) |
| Animations | Manual testing (Motion animations are visual) |

## Coverage Expectations

- **Business-critical actions**: 100% test coverage.
- **Controllers / feature routes**: Test happy path + validation errors + authorization.
- **UI components**: Test rendering + key interactions. Avoid testing implementation details.
- **No strict coverage threshold** enforced globally — quality over quantity.

---

## Testing as Delivery Validation

Testing is **part of delivery validation**, not optional polish. This section clarifies when and how to use each check.

### Implementation-time checks (iterate fast)

Run these during development to get rapid feedback. They do not need to be fully passing before you continue coding:

- `./vendor/bin/pint` — auto-fix formatting as you go
- `npx tsc --noEmit` — catch type errors early
- `php artisan test --filter=SpecificTest` — run targeted tests for the area you're changing
- `npm run test -- --watch` — watch mode for JS component tests

### Delivery-time checks (the full gate)

Run the complete pipeline **before marking any task done**. All steps must pass:

1. **PHP lint** (`./vendor/bin/pint --test`) — formatting is correct
2. **PHP static analysis** (`./vendor/bin/phpstan analyse`) — no type regressions
3. **JS/TS lint** (`npm run lint`) — no lint violations
4. **TypeScript check** (`npx tsc --noEmit`) — no type errors in the full project
5. **PHP tests** (`php artisan test --parallel`) — no regressions in server-side behavior
6. **JS tests** (`npm run test`) — no regressions in component behavior
7. **Build** (`npm run build`) — the production bundle compiles cleanly

> Tests, linting, type checks, and build validation are a **single quality gate** — none is sufficient alone. A codebase that passes tests but fails lint is not done. A codebase that passes lint but fails the build is not done.

See `context/project/04_RUNBOOK.md § Quality Gate Pipeline` for the full command reference and delivery report format.
