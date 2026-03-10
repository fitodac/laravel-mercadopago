# Security Baselines (reusable)

## Input Validation & Output Encoding

- **All user input is validated server-side** via Laravel Form Requests. Client-side validation is a UX convenience, never a security measure.
- **Never trust client data.** Validate types, ranges, formats, and business rules in Form Requests.
- **XSS prevention:**
  - React's JSX auto-escapes all interpolated values — this is your primary defense.
  - **Never** use `dangerouslySetInnerHTML` unless you've sanitized the input with a whitelist-based sanitizer (e.g., DOMPurify).
  - In Blade (Filament admin), use `{{ $var }}` (escaped), never `{!! $var !!}` unless explicitly sanitized.
- **SQL injection:** Use Eloquent or Query Builder exclusively. If raw SQL is absolutely necessary, always use parameterized bindings (`DB::select('... WHERE id = ?', [$id])`).

## Authentication & Authorization

### Authentication

- Use Laravel's built-in auth system (Sanctum for token-based, session for web).
- Inertia routes are protected by `auth` middleware by default.
- Filament uses its own auth guard — configure in `filament.php`.

### Authorization

- Use **Gates** for simple ability checks and **Policies** for model-based authorization.
- Apply policies in controllers: `$this->authorize('update', $post)`.
- In Filament, use resource-level `canViewAny()`, `canCreate()`, `canEdit()`, `canDelete()` methods or integrate a plugin like Filament Shield.
- Frontend: conditionally render UI based on permissions passed as Inertia props, but **always enforce on the server**.

## CSRF Protection

- Inertia handles CSRF automatically — the `@inertiaHead` directive injects the token.
- Ensure `VerifyCsrfToken` middleware is active (it is by default).
- No additional CSRF configuration is needed for Inertia forms.

## Secrets & Environment Variables

- **Never commit secrets to the repository.** All secrets go in `.env` (which is `.gitignore`d).
- Access secrets via `config()` helper, never through `env()` directly outside config files (important for config caching).
- For production: use a secrets manager (Vault, AWS Secrets Manager, etc.) or CI/CD environment variables.
- **Never expose server-side secrets to the frontend.** Inertia props should only contain data safe for the client.

## Dependency Policy

- **Audit regularly:**
  ```bash
  composer audit    # PHP dependencies
  npm audit         # JS dependencies
  ```
- **Pin major versions** in `composer.json` and `package.json`. Use `^` for minor/patch flexibility.
- **Review new dependencies** before adding: check maintenance status, download count, and known vulnerabilities.
- **Keep dependencies updated.** Run `composer update` and `npm update` regularly, reviewing changelogs for breaking changes.

## Headers & Transport Security

- Enforce HTTPS in production (`APP_URL=https://...`).
- Set security headers via middleware (or server config):
  - `X-Content-Type-Options: nosniff`
  - `X-Frame-Options: DENY` (unless embedding is needed)
  - `Strict-Transport-Security: max-age=31536000; includeSubDomains`
  - `Referrer-Policy: strict-origin-when-cross-origin`
- Content Security Policy (CSP): configure per project based on actual asset origins.

## File Uploads

- Validate file type, size, and MIME type in Form Requests.
- Store uploads outside the public directory or use signed URLs.
- Never serve user-uploaded files with their original filename; use UUIDs.
- Scan for malware in high-risk environments.

## Rate Limiting

- Apply rate limiting to login, registration, and API endpoints via Laravel's `ThrottleRequests` middleware.
- Default: `60 requests/minute` for authenticated, `10/minute` for auth endpoints.
- Customize in `app/Providers/RouteServiceProvider.php` or `bootstrap/app.php`.
