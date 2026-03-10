# Style Guide (reusable)

## PHP

- Follow **PSR-12** via [Laravel Pint](https://laravel.com/docs/pint) with default Laravel preset.
- Use `declare(strict_types=1);` in all non-Laravel-generated files.
- Use **typed properties** and **return types** everywhere.
- Array syntax: short arrays (`[]`), trailing commas on multiline.
- String interpolation: use `"Hello {$name}"` (braced syntax) for clarity.

```php
declare(strict_types=1);

final class CreateUserAction
{
    public function execute(CreateUserData $data): User
    {
        return User::create([
            'name'  => $data->name,
            'email' => $data->email,
        ]);
    }
}
```

## TypeScript / JSX

- **Single quotes**, **trailing commas**, **2-space indent**, **semicolons**.
- Max line length: 100 chars (soft limit — don't break readability to enforce it).
- Component files: one component per file; filename matches the component name.
- Hooks: prefix custom hooks with `use` (`useAuth`, `useDebounce`).
- Avoid `any`. Use `unknown` + type narrowing if the type is truly dynamic.

```tsx
import { motion } from 'motion/react'
import { cn } from '@/lib/utils'

interface CardProps {
  title: string
  className?: string
  children: React.ReactNode
}

export function Card({ title, className, children }: CardProps) {
  return (
    <motion.div
      initial={{ opacity: 0, y: 8 }}
      animate={{ opacity: 1, y: 0 }}
      className={cn('rounded-xl border p-6', className)}
    >
      <h3 className="text-lg font-semibold">{title}</h3>
      {children}
    </motion.div>
  )
}
```

## CSS (Tailwind 4)

- Entry point: `resources/css/app.css`.
- First line: `@import "tailwindcss";`
- Tokens inside `@theme { ... }` block using CSS custom properties.
- Custom component classes: use `@utility` directive (not `@layer components` from TW3).
- No `tailwind.config.js` — all theme config is CSS-side.

```css
@import "tailwindcss";

@theme {
  --color-primary: oklch(0.6 0.25 260);
  --color-surface: oklch(0.15 0.01 260);
  --font-sans: 'Inter', sans-serif;
  --radius-lg: 0.75rem;
}
```

## Filament (PHP)

- Follow Filament's conventions for Resources, Pages, and Widgets.
- Resource class names: `{Model}Resource`, e.g., `UserResource`.
- Put relation managers in the resource's `RelationManagers/` subdirectory.
- Use the `->label()`, `->placeholder()`, and `->helperText()` methods for form UX.

## File Organization Recap

| Path | Content | Convention |
|---|---|---|
| `app/Actions/` | Single-responsibility action classes | PascalCase |
| `app/Http/Controllers/` | Inertia controllers | PascalCase |
| `app/Http/Requests/` | Form Request validation | PascalCase |
| `app/Filament/Resources/` | Filament admin resources | PascalCase |
| `resources/js/Pages/` | Inertia page components | PascalCase, `export default` |
| `resources/js/Components/` | Shared React components | PascalCase, named exports |
| `resources/js/components/ui/` | Shadcn/ui primitives | kebab-case file, PascalCase export |
| `resources/js/lib/` | Hooks, utils, constants | camelCase |
| `resources/css/` | Tailwind entry + any custom CSS | kebab-case |
