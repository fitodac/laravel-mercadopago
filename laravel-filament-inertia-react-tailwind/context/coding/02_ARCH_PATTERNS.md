# Architecture Patterns (reusable)

## ✅ Preferred Patterns

### Backend

- **Form Requests for validation.** All input validation goes through `FormRequest` classes — never validate inline in controllers.

  ```php
  // ✅ Good
  public function store(StorePostRequest $request): RedirectResponse { ... }

  // ❌ Bad
  public function store(Request $request): RedirectResponse
  {
      $request->validate(['title' => 'required']); // inline validation
  }
  ```

- **Action classes for business logic.** Extract business logic into single-method action classes. Controllers call actions, not the other way around.

  ```php
  // ✅ Good
  class CreatePostAction
  {
      public function execute(CreatePostData $data): Post { ... }
  }
  ```

- **Shared data via `HandleInertiaRequests` middleware.** Global data (auth user, flash messages, notifications) is injected through `share()` — never duplicated in controllers.

- **Eager loading** relationships with `->with()` to prevent N+1 queries. Use `->loadMissing()` when conditional.

- **Resources / Data Transfer Objects** for shaping data sent to Inertia. Don't pass raw Eloquent models with all attributes/relations to the frontend.

### Frontend

- **Inertia `useForm` for form handling.** Don't use raw `fetch` or `axios` — `useForm` handles CSRF, validation errors, loading state, and redirects.

  ```tsx
  // ✅ Good
  const form = useForm({
    title: '',
    body: '',
  })

  function submit(e: FormEvent) {
    e.preventDefault()
    form.post(route('posts.store'))
  }
  ```

- **Persistent layouts** via Inertia's `layout` property. Avoid re-mounting shared layout components on navigation.

  ```tsx
  Dashboard.layout = (page: React.ReactNode) => (
    <AppLayout>{page}</AppLayout>
  )
  ```

- **Motion variants defined as constants** outside the component tree. Use `useMemo` for dynamic variants.

  ```tsx
  // ✅ Good — module-level constant
  const fadeIn = {
    initial: { opacity: 0, y: 8 },
    animate: { opacity: 1, y: 0 },
    transition: { duration: 0.3 },
  }

  export function Card() {
    return <motion.div {...fadeIn}>...</motion.div>
  }
  ```

- **`LazyMotion` + `domAnimation`** at the app root to reduce bundle size. Use `domMax` only if gesture support is needed.

  ```tsx
  import { LazyMotion, domAnimation } from 'motion/react'

  export default function App({ children }) {
    return (
      <LazyMotion features={domAnimation}>
        {children}
      </LazyMotion>
    )
  }
  ```

- **`AnimatePresence`** wrapping conditional renders for exit animations. Always provide a unique `key`.

- **`cn()` helper** for merging Tailwind classes (typically using `clsx` + `tailwind-merge`).

  ```tsx
  import { cn } from '@/lib/utils'

  <div className={cn('p-4 rounded-lg', isActive && 'bg-primary/10')} />
  ```

### Styling

- **CSS-first Tailwind 4 tokens.** All theme customizations go in `@theme { }` inside `app.css`.

  ```css
  @import "tailwindcss";

  @theme {
    --color-primary: oklch(0.6 0.25 260);
    --color-secondary: oklch(0.7 0.15 150);
  }
  ```

- **Shadcn components are editable source.** Modify them directly in `components/ui/`. Don't fight the abstraction.

- **HeroUI components are npm packages.** If you need to customize beyond props, wrap them in a project component.

### Inertia Routing Conventions

- Routes use **resourceful naming**: `posts.index`, `posts.show`, `posts.store`, `posts.update`, `posts.destroy`.
- Use **Ziggy** for generating URLs in React: `route('posts.show', { post: id })`.
- Group related routes with prefix + middleware:

  ```php
  Route::middleware(['auth', 'verified'])->group(function () {
      Route::resource('posts', PostController::class);
      Route::resource('comments', CommentController::class)->only(['store', 'destroy']);
  });
  ```

- Controller methods return `Inertia::render()` for GET and `redirect()->route()` for POST/PUT/DELETE.
- Page component path in `Inertia::render()` must match the file path under `resources/js/Pages/` exactly (case-sensitive).

### Action / Service Structure

- **Actions** = single public method (`execute` or `__invoke`), one responsibility, stateless.
- **Services** = multiple related methods when a single action is too granular (e.g., `PaymentService` with `charge()`, `refund()`, `getHistory()`).
- Use **constructor injection** for dependencies (Laravel auto-resolves).

  ```php
  final class CreatePostAction
  {
      public function __construct(
          private readonly SlugGenerator $slugs,
      ) {}

      public function execute(CreatePostData $data): Post
      {
          return Post::create([
              'title' => $data->title,
              'slug'  => $this->slugs->generate($data->title),
              'body'  => $data->body,
          ]);
      }
  }
  ```

- **Data Transfer Objects** (Spatie `laravel-data` or plain classes) to pass validated data from Form Requests to Actions — not raw arrays.
- Actions are testable in isolation (unit tests). Controllers are tested via feature/HTTP tests.

### Filament vs Inertia Boundaries

| Concern | Where it lives | Why |
|---|---|---|
| CRUD for admin users | **Filament** (Resources) | Built-in tables, forms, filters |
| Bulk operations / imports | **Filament** (Actions, Jobs) | Admin-only, batch processing |
| Reports / dashboards (admin) | **Filament** (Widgets, Pages) | Internal metrics, charts |
| User-facing flows | **Inertia + React** | Interactive UX, animations, custom design |
| User-facing forms | **Inertia + React** | Custom validation UX, multi-step flows |
| Public pages / marketing | **Inertia + React** | Full creative control |
| **Shared logic** | **Actions / Services** | Both Filament and Inertia call the same Action classes |

- **Never** import React components into Filament or Livewire components into React.
- **Shared domain logic** (Actions, Models, Notifications, Events) is the glue between both surfaces. Never duplicate business logic.

### Package / Plugin Structure

Route: `packages/<Vendor>/<PackageName>/`

When using a `packages/` directory for modular code:

```
packages/
└── acme/
    └── blog/
        ├── composer.json          # Package metadata
        ├── src/
        │   ├── BlogServiceProvider.php
        │   ├── Actions/
        │   ├── Models/
        │   ├── Http/
        │   │   ├── Controllers/
        │   │   └── Requests/
        │   └── Filament/
        │       └── Resources/
        ├── resources/
        │   ├── js/                 # React components (if any)
        │   └── views/             # Blade views (if any)
        ├── routes/
        │   └── web.php            # Package routes (NOT in project routes/)
        ├── config/
        │   └── blog.php           # Package config (NOT in project config/)
        ├── database/
        │   └── migrations/
        └── tests/
```

#### Canonical `composer.json`

```json
{
    "name": "acme/blog",
    "description": "Blog package for the project",
    "type": "library",
    "autoload": {
        "psr-4": {
            "Acme\\Blog\\": "src/"
        }
    },
    "extra": {
        "laravel": {
            "providers": [
                "Acme\\Blog\\BlogServiceProvider"
            ]
        }
    }
}
```

#### ServiceProvider responsibilities

```php
final class BlogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/blog.php', 'blog');
    }

    public function boot(): void
    {
        // Routes — owned by the package
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Migrations — owned by the package
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Views — owned by the package
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'blog');

        // Publishable config (optional)
        $this->publishes([
            __DIR__ . '/../config/blog.php' => config_path('blog.php'),
        ], 'blog-config');
    }
}
```

#### Registration in root `composer.json`

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "packages/acme/blog"
        }
    ],
    "require": {
        "acme/blog": "@dev"
    }
}
```

Then run `composer update acme/blog`.

#### Key rules

- Each package has its own `ServiceProvider`, routes, config, and migrations.
- **Routes and config must live inside the package.** Never register package routes or config in the project's `routes/**` or `config/**`. The ServiceProvider loads them.
- Register packages in the root `composer.json` via path repositories.
- Packages can provide their own Filament resources and Inertia pages.
- The **Path Boundary Policy** (see `02_STANDARDS.md`) treats `packages/**` as an allowed root — all package code is safe to modify without special permission.

---

## ❌ Anti-Patterns to Avoid

### Backend

- ❌ **No raw SQL** unless Eloquent/Query Builder genuinely can't express the query (rare). If you must, use parameterized bindings.
- ❌ **No business logic in controllers.** Controllers should be thin: validate → delegate → respond.
- ❌ **No direct model mass-assignment without `$fillable`.** Always define `$fillable` or `$guarded` on models.
- ❌ **No storing secrets in code.** Use `.env` + `config()` exclusively.

### Frontend

- ❌ **No `useState` for server data.** If the data comes from Inertia props, use it directly. Use `useState` only for transient UI state (modal open, input focus, etc.).
- ❌ **No direct DOM manipulation.** Use React refs + Motion — never `document.querySelector` in components.
- ❌ **No mixing Shadcn and HeroUI for the same component type.** Pick one source per component (e.g., if you use Shadcn Button, don't also import HeroUI Button).
- ❌ **No `framer-motion` imports.** The package was renamed to `motion`. Import from `motion/react`.
- ❌ **No animating layout-triggering properties** (`width`, `height`, `top`, `left`, `padding`, `margin`). Use `transform` (`x`, `y`, `scale`) + `opacity` only.
- ❌ **No inline animation variant objects** inside JSX or component bodies. Define them as module-level constants.

### Styling

- ❌ **No `tailwind.config.js`** for theme configuration (Tailwind 4 uses CSS-first config). Only use `@config` if a third-party plugin absolutely requires it.
- ❌ **No `@apply` abuse.** Use it sparingly; prefer utility classes directly in JSX.
- ❌ **No custom CSS for things Tailwind already handles.** Check the docs before writing custom CSS.
