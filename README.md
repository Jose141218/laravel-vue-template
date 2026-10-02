# Laravel + Vue Starter Template (Screaming Architecture & Spatie v8)

A modern, production-ready full-stack starter template built with **Laravel 13**, **Inertia.js v3**, **Vue 3 (Composition API & Script Setup)**, **TypeScript**, **Tailwind CSS v4**, **shadcn-vue**, and **Spatie Permission v8**.

---

## 🚀 Tech Stack

- **Backend:** Laravel 13.x (PHP 8.3+)
- **Frontend:** Vue 3.5+ with TypeScript (`<script setup lang="ts">`)
- **Hybrid Monolith:** Inertia.js v3 (native HTTP client, dynamic layouts)
- **Styling:** Tailwind CSS v4 + `@tailwindcss/vite` + shadcn-vue components + Admin One responsive table styles
- **Security & RBAC:** `spatie/laravel-permission` v8 with wildcard permission support (`module.*`) and custom `modules` table
- **Identifiers:** Native UUID v7 (`HasUuids`) on business models
- **Frontend Routes:** Ziggy with auto-generated type definitions (`ziggy:generate --types`)
- **Testing:** Vitest + `@vue/test-utils` + happy-dom (Frontend) & PHPUnit (Backend)
- **Package Manager:** pnpm
- **Code Quality:** ESLint (Flat Config), Prettier with Tailwind v4 plugin, Laravel Pint, and PHPStan (high level)

---

## 🏛️ Screaming Architecture (Project Structure)

The frontend under `resources/js/` is organized by explicit domain modules and single responsibilities:

```
resources/
├── css/
│   ├── app.css              # Tailwind v4 configuration + theme variables (dark/light)
│   └── _table.css           # Responsive .data-table card styling for mobile devices
└── js/
    ├── app.ts               # Inertia v3 bootstrapper and plugin setup
    ├── components/          # Reusable components
    │   ├── common/          # Shared components (SearchBar, SortableHeader, Pagination, ConfirmDialog, StatusBadge)
    │   └── ui/              # Design components based on shadcn-vue / Reka UI
    ├── composables/         # Shared composables (usePermissions, useFilters, useAppearance, useCurrentUrl)
    ├── layouts/             # Global layouts (AppLayout, AuthLayout, SettingsLayout)
    ├── lib/                 # Utilities (cn, flashToast, helpers)
    ├── modules/             # Application modules (Modular Screaming Architecture)
    │   ├── auth/            # Authentication (pages/, components/)
    │   ├── dashboard/       # Dashboard (pages/, components/)
    │   ├── security/        # Security & RBAC Module
    │   │   ├── modules/     # Module groups catalog (pages/, interfaces/)
    │   │   ├── roles/       # Role and permission management (pages/, interfaces/)
    │   │   ├── permissions/ # Atomic permissions catalog (pages/, interfaces/)
    │   │   └── users/       # User administration (pages/, interfaces/)
    │   ├── settings/        # User profile & preferences (pages/, components/, composables/)
    │   └── welcome/         # Landing / Welcome page (pages/)
    ├── types/               # Global TypeScript definitions and domain models
    ├── ziggy.d.ts           # Ziggy TypeScript route definitions
    └── ziggy.js             # Generated route map from artisan
```

### Module Conventions (Frontend)
Each domain module or functional submodule is self-contained according to its needs:
- **`pages/`**: Primary Inertia views and pages (`Index.vue`, `Create.vue`, `Edit.vue`, etc.).
- **`components/`**: Isolated, domain-specific components for this module.
- **`composables/`**: Dedicated reactive state and logic for the module.
- **`interfaces/`**: TypeScript data contracts for API responses and models.
- **`helpers/`**: Helper and formatting functions specific to the module.

### Controller Conventions (Backend)
Domain controllers inherit from the base `Controller` and define explicit view source paths and route names:
```php
class UserController extends Controller
{
    protected string $source = 'security/users/pages/';
    protected string $routeName = 'security.users';

    public function index(): Response
    {
        return Inertia::render($this->source . 'Index', [ ... ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        // ...
        return to_route("{$this->routeName}.index")
            ->with('success', 'User created successfully.');
    }
}
```

---

## 🔑 Key Features

### 1. Permissions & Roles (Spatie v8 + Wildcards + Modules)
- **`modules` Table:** Categorizes permissions logically (`seg`, `cat`, `sys`, etc.).
- **Wildcard Permissions:** Supports wildcard grants such as `users.*`, automatically providing access to `users.index`, `users.create`, `users.edit`, and `users.delete`.
- **Typed `usePermissions` Composable:**
  ```vue
  <script setup lang="ts">
  import { useCan, useCanAny, useRole } from '@/composables/usePermissions';

  const canEdit = useCan('users.edit');
  const isAdmin = useRole('admin');
  </script>
  ```
- **Directives & Route Middleware:**
  ```php
  Route::resource('users', UserController::class)->middleware('permission:users.index');
  ```

### 2. UUID v7 on Business Models
- Unpredictable identifiers preventing IDOR attacks and URL enumeration.
- Native `use Illuminate\Database\Eloquent\Concerns\HasUuids;` trait generating time-ordered UUIDs (v7), ensuring optimal B-Tree indexing performance in MySQL, PostgreSQL, and SQLite.

### 3. Reactive Data Tables & Filters (`useFilters`)
- Composable featuring debounced search (VueUse), column sorting, configurable rows-per-page, and seamless synchronization with Laravel pagination.

---

## 🌐 Environment & Local URL Configuration (`APP_URL` vs `vite.config.ts`)

### Agnostic Vite Configuration
The [vite.config.ts](vite.config.ts) file deliberately **does not** hardcode URLs or domains. This keeps the starter template completely portable across any local development environment:

- `php artisan serve` (`http://localhost:8000`)
- **Laragon** (`http://laravel-vue-template.test` or custom virtual hosts)
- **Laravel Herd** / **Laravel Valet**
- **Docker** / **Laravel Sail**

### Setting Your Application URL
The application URL belongs to the environment and is configured exclusively via the `APP_URL` variable in your [`.env`](.env.example) file:

```env
# Default development server:
APP_URL=http://localhost:8000

# When using Laragon, Herd, or Valet virtual hosts:
APP_URL=http://laravel-vue-template.test
```

The `laravel-vite-plugin` automatically reads `APP_URL` from `.env` to display the active URL when running `pnpm dev` and outputs the `public/hot` manifest. Laravel's `@vite` directive reads this file to inject hot-module reload (HMR) assets without requiring changes to `vite.config.ts`.

### When Should You Customize `vite.config.ts`?
Anyone extending or using this template only needs to modify `vite.config.ts` for specialized local infrastructure:

- **Local HTTPS / SSL certificates (Laragon SSL / Valet / Herd):**
  ```ts
  export default defineConfig({
      // ...
      server: {
          // Configure certificates or secure HMR if required by your SSL proxy
      },
  });
  ```
- **Local Network (LAN) / Docker / Mobile device testing:**
  ```ts
  export default defineConfig({
      // ...
      server: {
          host: true, // Listens on 0.0.0.0 for LAN or container access
      },
  });
  ```

---

## 🛠️ Development Commands

### Initial Setup
```bash
composer setup
```
> Runs dependency installation, creates `.env`, generates app key, ensures SQLite database, runs migrations with initial seeders, and builds frontend assets.

### Start Development Server
```bash
# Terminal 1 (Frontend Vite HMR):
pnpm dev

# Terminal 2 (Backend Server):
php artisan serve
```
*Note: You can also run `composer dev` to launch the unified development watcher.*

### Run Tests (Frontend + Backend)
```bash
# Frontend Unit Tests (Vitest)
pnpm test:unit

# Backend Feature & Unit Tests (PHPUnit)
php artisan test

# Full CI Quality Check Suite
composer ci:check
```

### Generate Ziggy Route Types
```bash
php artisan ziggy:generate --types
```

### Code Formatting and Static Analysis
```bash
# Backend (PHP)
vendor/bin/pint
vendor/bin/phpstan analyse

# Frontend (Vue / TS / CSS)
pnpm lint:check
pnpm format:check
pnpm types:check
```

---

## 👤 Initial Seeders & Test Accounts

Run `php artisan migrate:fresh --seed` to initialize default accounts:

- **Administrator:** `admin@sw.com` / `password` (`admin` role with all permissions)
- **Standard User:** `usuario@sw.com` / `password` (`usuario` role with basic access)
