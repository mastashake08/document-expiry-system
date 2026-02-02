# Document Expiry System - AI Agent Guidelines

## Project Overview
Laravel 12 + Vue 3 + Inertia.js application for managing documents with expiration dates. Users upload documents with metadata (notes, expiration_date, owner contact info) and receive SMS/email notifications when documents near expiration.

## Tech Stack Architecture

### Backend (Laravel 12)
- **PHP 8.2+** with Laravel 12 framework
- **Fortify** for authentication (login, register, password reset, 2FA)
- **Inertia.js** server adapter - all routes return Inertia responses (`Inertia::render()`)
- **Testing**: Pest (preferred), not PHPUnit - see `tests/Pest.php` for configuration
- **Code style**: Laravel Pint (PSR-12 based) - run `composer lint` to fix

### Frontend (Vue 3 + TypeScript)
- **Vue 3** with Composition API and TypeScript
- **Inertia.js** client - pages in `resources/js/pages/`, no traditional Vue Router
- **Styling**: Tailwind CSS v4 (via Vite plugin, no config file)
- **Components**: Reka UI (headless) + custom UI library in `resources/js/components/ui/`
- **Icons**: lucide-vue-next
- **Type-safe routing**: Laravel Wayfinder generates TypeScript route helpers

## Critical Development Workflows

### Local Development
```bash
# First time setup
composer setup  # Installs deps, generates key, runs migrations, builds frontend

# Daily development (runs server + queue + logs + vite in parallel)
composer dev

# With SSR support
composer dev:ssr
```

**Never run** `php artisan serve` or `npm run dev` separately - use `composer dev` which orchestrates all services via concurrently.

### Code Quality & Testing
```bash
composer lint        # Fix PHP code style with Pint
composer test:lint   # Check PHP style (CI mode)
composer test        # Runs Pint check + Pest tests
npm run format       # Fix JS/Vue formatting with Prettier
npm run lint         # Fix with ESLint
```

### Docker (Laravel Sail)
Uses `compose.yaml` with PHP 8.5, MySQL 8.4. See `docker/` for custom images.

## Project Structure Patterns

### Route Organization
- **`routes/web.php`**: Public routes and dashboard
- **`routes/settings.php`**: User settings routes (profile, password, 2FA, appearance)
- Auth routes handled by Fortify, views customized in `FortifyServiceProvider`

### Controller Pattern
Controllers in `app/Http/Controllers/Settings/` follow resource-like methods:
- `edit()`: Return Inertia page with data
- `update()`: Process form, validate via FormRequest, redirect
- `destroy()`: Delete resource, use custom FormRequest for authorization

Example: `ProfileController` uses `ProfileUpdateRequest` and `ProfileDeleteRequest`

### Inertia Page Components
- **Location**: `resources/js/pages/`
- **Naming**: PascalCase, match route path (e.g., `settings/Profile.vue` for `/settings/profile`)
- **Props**: Typed via `defineProps<{ ... }>()`
- **Forms**: Use Inertia form helpers for validation errors

### Vue Component Organization
```
resources/js/
├── components/
│   ├── ui/             # Reka UI wrappers (Button, Sheet, Dialog, etc.)
│   ├── AppShell.vue    # Main layout with header/sidebar
│   ├── AppHeader.vue   # Top navigation
│   └── Nav*.vue        # Navigation components
├── composables/
│   ├── useAppearance.ts    # Dark/light theme management
│   ├── useTwoFactorAuth.ts # 2FA setup logic
│   └── useCurrentUrl.ts    # Route awareness
├── layouts/            # Page layouts (if any)
├── lib/
│   └── utils.ts        # cn() for class merging, toUrl() for Inertia links
└── pages/              # Inertia page components
```

### Composables Pattern
Export explicit return types and implement reactive state with `ref()`/`computed()`. Example:
```typescript
export type UseFeatureReturn = {
    state: Ref<string>;
    action: () => void;
};

export function useFeature(): UseFeatureReturn { ... }
```

### UI Component Exports
UI components use barrel exports: `components/ui/button/index.ts` exports `Button.vue`. Import like:
```typescript
import { Button } from '@/components/ui/button';
```

### Utility Functions
- **`cn(...inputs)`**: Merge Tailwind classes with conflict resolution (clsx + tailwind-merge)
- **`toUrl(href)`**: Convert Inertia link props to strings

## Authentication & Security

### Fortify Configuration
- Views customized in `app/Providers/FortifyServiceProvider.php` to return Inertia pages
- 2FA enabled via `TwoFactorAuthenticatable` trait on User model
- Password validation rules in `app/Concerns/PasswordValidationRules.php`
- Production passwords: 12+ chars, mixed case, symbols, uncompromised (see `AppServiceProvider`)

### Protected Routes
- `auth` middleware: Authenticated users only
- `verified` middleware: Email verified users only
- Settings routes combine both: `->middleware(['auth', 'verified'])`

## Database Conventions

- **Migrations**: Timestamps in filename, use `Schema::create()` / `Schema::table()`
- **Models**: Place in `app/Models/`, use traits (`HasFactory`, `Notifiable`)
- **Factories**: `database/factories/`, used for seeding/testing
- Current schema: `users` (with 2FA columns), `password_reset_tokens`, `sessions`, `jobs`, `cache`

## Frontend Type Safety

### Wayfinder Routes
Generates type-safe route helpers from Laravel routes. Access via auto-imported functions:
```typescript
// Instead of route('profile.edit')
wayfinder.route('profile.edit')
```

### TypeScript Configuration
- Strict mode enabled
- Path aliases: `@/` maps to `resources/js/`
- Vue SFC support via `vue-tsc`

## Key Dependencies to Know

### Laravel Packages
- `laravel/fortify`: Authentication scaffolding
- `inertiajs/inertia-laravel`: Server-side adapter
- `laravel/wayfinder`: Type-safe routing for frontend

### Vue/JS Libraries
- `@inertiajs/vue3`: Client-side Inertia adapter
- `reka-ui`: Headless component primitives (Dialog, Sheet, Sidebar)
- `class-variance-authority`: Component variant styling
- `@vueuse/core`: Vue composition utilities

## Common Tasks

### Adding a New Settings Page
1. Create route in `routes/settings.php` with `auth` + `verified` middleware
2. Create controller in `app/Http/Controllers/Settings/`
3. Create FormRequest(s) for validation
4. Create Vue page in `resources/js/pages/settings/`
5. Add navigation link in `NavMain.vue` or relevant component

### Adding a New Model
1. Create migration: `php artisan make:migration create_tablename_table`
2. Create model: `php artisan make:model ModelName`
3. Create factory: `php artisan make:factory ModelNameFactory`
4. Add relationships, casts, fillable properties

### Adding UI Components
Follow Reka UI pattern: create wrapper components in `components/ui/[component]/`, export via `index.ts`, use `cn()` for styling with Tailwind.

## Important Notes

- **No traditional Vue Router**: Inertia handles navigation
- **SSR Support**: Optional, configured but not required for development
- **Queue Workers**: Required for background jobs (notifications)
- **Appearance Theme**: Managed by `useAppearance()`, persists to localStorage and DOM
- **Form Validation**: Server-side via FormRequests, errors passed to Inertia automatically
- **Testing**: Prefer Pest over PHPUnit syntax
