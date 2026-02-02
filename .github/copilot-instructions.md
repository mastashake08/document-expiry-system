# Document Expiry System - AI Agent Guidelines

## Project Overview
Laravel 12 + Vue 3 + Inertia.js application for managing documents with expiration dates. Users upload documents with metadata (notes, expiration_date, owner contact info) and receive SMS/email notifications when documents near expiration.

**Architecture Philosophy**: API-driven with TDD practices and Service/Action pattern for business logic.

## Tech Stack Architecture

### Backend (Laravel 12)
- **PHP 8.2+** with Laravel 12 framework
- **Fortify** for authentication (login, register, password reset, 2FA)
- **Sanctum** for API token authentication (`auth:sanctum` middleware)
- **Inertia.js** server adapter for web routes - returns Inertia responses (`Inertia::render()`)
- **Testing**: Pest (preferred), not PHPUnit - see `tests/Pest.php` for configuration
- **Code style**: Laravel Pint (PSR-12 based) - run `composer lint` to fix
- **TDD Approach**: Write tests first in `tests/Feature/` and `tests/Unit/`, use `RefreshDatabase` trait

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

## Test-Driven Development (TDD)

### Testing Philosophy
Write tests BEFORE implementing features. Tests live in `tests/`:
- **Feature tests**: HTTP requests, full application flow (`tests/Feature/`)
- **Unit tests**: Isolated business logic, services, actions (`tests/Unit/`)

### Pest Test Structure
Use Pest syntax (not PHPUnit classes):
```php
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('profile information can be updated', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    
    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));
    
    $user->refresh();
    expect($user->name)->toBe('Test User');
});
```

### Test Organization
- Group tests by feature: `tests/Feature/Auth/`, `tests/Feature/Settings/`
- Use factories for test data: `User::factory()->create()`
- Use `RefreshDatabase` trait to reset database between tests
- Test both success and failure paths
- API tests should check JSON structure and status codes

### Running Tests
```bash
composer test        # Run all tests with Pint check
php artisan test     # Run tests only
php artisan test --filter=ProfileUpdateTest  # Run specific test
```

## Project Structure Patterns

### Route Organization
- **`routes/api.php`**: RESTful API endpoints with Sanctum authentication
- **`routes/web.php`**: Public routes and dashboard (Inertia pages)
- **`routes/settings.php`**: User settings routes (profile, password, 2FA, appearance)
- Auth routes handled by Fortify, views customized in `FortifyServiceProvider`

**Web Controllers** (e.g., `app/Http/Controllers/Settings/`) return Inertia pages:
- `edit()`: Return Inertia page with data
- `update()`: Process form, validate via FormRequest, redirect
- `destroy()`: Delete resource, use custom FormRequest for authorization

Example: `ProfileController` uses `ProfileUpdateRequest` and `ProfileDeleteRequest`

**API Controllers** return JSON responses, delegate business logic to Services/Actions:
```php
public function store(StoreDocumentRequest $request)
{
    $document = $this->documentService->create($request->validated());
    return new DocumentResource($document);
}
```

### Service/Action Layer Pattern
Business logic belongs in `app/Actions/` or `app/Services/`, NOT controllers:

**Actions**: Single-responsibility classes implementing Fortify contracts or specific operations
- Location: `app/Actions/Fortify/` (e.g., `CreateNewUser`, `ResetUserPassword`)
- Pattern: Implement contract interface, single public method
- Example: `CreateNewUser` validates and creates users

**Services**: When you add business logic, create service classes for complex operations:
- Location: `app/Services/` (create this directory)
- Pattern: Dependency injection in constructor, multiple related methods
- Use for: Document management, notification sending, file processing
- Example structure:
```php
class DocumentService
{
    public function create(array $data): Document { }
    public function notifyExpiry(Document $document): void { }
}
```

### Validation Pattern
Use reusable traits for common validation rules:
- `app/Concerns/PasswordValidationRules.php`: Password validation
- `app/Concerns/ProfileValidationRules.php`: Name/email validation
- Create similar traits for domain-specific validation (e.g., `DocumentValidationRules`)
```
Controllers should return JSON responses or use API Resources for structured output.

### Controller Pattern
Controllers in `app/Http/Controllers/Settings/` follow resource-like methods:
- `laravel/sanctum`: API token authentication
- `inertiajs/inertia-laravel`: Server-side adapter
- `laravel/wayfinder`: Type-safe routing for frontend
- `pestphp/pest`: Testing framework with Laravel plugin redirect
- `destroy()`: Delete resource, use custom FormRequest for authorization

Example: `ProfileController` uses `ProfileUpdateRequest` and `ProfileDeleteRequest`

### Inertia Page Components
- **Location**: `resources/js/pages/`
- **Naming**: PascalCase, match route path (e.g., `settings/Profile.vue` for `/settings/profile`)
- **Props**: TypeAPI Resource (TDD Workflow)
1. **Write test first** in `tests/Feature/`:
   ```php
   test('can create document', function () {
       $user = User::factory()->create();
       $response = $this->actingAs($user, 'sanctum')
           ->postJson('/api/documents', ['title' => 'Test']);
       $response->assertCreated();
   });
   ```
2. Create migration: `php artisan make:migration create_documents_table`
3. Create model with factory: `php artisan make:model Document -f`
4. Create API controller: `php artisan make:controller Api/DocumentController --api`
5. Create service in `app/Services/DocumentService.php` for business logic
6. Create FormRequest: `php artisan make:request StoreDocumentRequest`
7. Create API Resource: `php artisan make:resource DocumentResource`
8. Add route to `routes/api.php` with `auth:sanctum` middleware
9. Run test: `php artisan test --filter=DocumentTest`
10. Implement controller method calling service
11. Verify test passes

### Adding a New Settings Page
1. **Write test first** in `tests/Feature/Settings/`
2. Create route in `routes/settings.php` with `auth` + `verified` middleware
3. Create controller in `app/Http/Controllers/Settings/`
4. Create FormRequest(s) for validation
5. Create Vue page in `resources/js/pages/settings/`
6. Add navigation link in `NavMain.vue` or relevant component
7. Verify test passes

### Adding Business Logic
1. Create Action class in `app/Actions/` for single-purpose operations
2. Create Service class in `app/Services/` for complex/multi-step operations
3. Use dependency injection in constructors
4. Write unit tests in `tests/Unit/` for complex logic
5. Controllers should be thin - delegate to Actions/Servic
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
