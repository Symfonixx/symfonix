### Project Overview
Symfonix is a modular Laravel 13 business management platform for agencies, combining a multilingual public CMS, CRM (quotes, WhatsApp/email marketing, forecasts), project management, multi-currency finance, tax, reporting, client portal, HR (including fingerprint attendance), and support ticketing.

### Tech Stack
- **Backend:** PHP 8.3+, Laravel 13, `nwidart/laravel-modules`
- **Frontend:** Blade, Inertia.js + Vue 3, Livewire 4, Tailwind CSS, Bootstrap 5 (Metronic theme), Vite
- **Database & State:** MySQL 8+ / MariaDB, Redis, Spatie Permission, Spatie Laravel Data, Spatie Translatable
- **Auth & Packages:** Fortify (2FA), DomPDF, BotMan, Telescope, Pulse, Localization (`mcamara`), WhatsApp Cloud API, ZKTeco fingerprint

### Directory Map
- `app/` — Global HTTP middleware, base console commands, framework bootstrap
- `Modules/` — Modular domains (`CRM`, `Finance`, `Tax`, `Reporting`, `Project`, `User`, `Base`, `Cms`, `Product`, `Support`, `Team`, `Testimonial`, `SearchEngine`)
  - `Modules/<Module>/app/Http/Controllers/` — Admin & API route controllers
  - `Modules/<Module>/app/Models/` — Eloquent models with relations & scopes
  - `Modules/<Module>/app/Repositories/` — Data access & query abstraction
  - `Modules/<Module>/app/Services/` — Domain & business logic
  - `Modules/<Module>/resources/` — Module Blade views, Inertia/Vue pages, and assets
  - `Modules/<Module>/routes/` — Module route definitions (`web.php`, `api.php`)
  - `Modules/<Module>/database/` — Module migrations and seeders
- `config/` — System and package configuration files
- `resources/` — Global Blade layouts, components, and shared Vue shells

### Core Conventions & Patterns
- **Standards:** PSR-12, strict PHP 8.3 typing on properties, parameters, and return types.
- **Architecture & Patterns:**
  - **Modular DDD:** Keep domain logic encapsulated within `Modules/<Module>`.
  - **Service & Repository:** Thin controllers delegating queries to Repositories and business processes to Services.
  - **DTOs & Validation:** Use Form Requests and Spatie Laravel Data for input validation/transfer.
  - **Naming:** PascalCase for Classes/Interfaces, camelCase for methods/variables, snake_case for DB columns/route names/Blade files.
  - **Multilingual:** Use `mcamara/laravel-localization` routes and Spatie Translatable models.

### Key Commands
- **Tests:** `php artisan test` or `./vendor/bin/phpunit`; Playwright E2E via `npm run test:e2e` (see `tests/README.md`)
- **Code Style:** `./vendor/bin/pint`
- **Migrations:** `php artisan migrate` or `php artisan module:migrate <Module>`
- **Module Generator / Seed:** `php artisan module:seed <Module>` | `php artisan app:install`
- **Build:** `npm run dev` | `npm run build`

### Cursor AI Rules & Token Optimization
- **Be Concise:** No pleasantries, boilerplate conversational framing, or recap summaries.
- **Targeted Diffs Only:** Do not reprint whole untouched files; output only modified functions, concise diffs, or newly created files.
- **Module Respect:** Always place new domain logic, models, views, and migrations within the relevant `Modules/<Name>/` path instead of root `app/`.
- **Follow Established Patterns:** Use existing Repository/Service structures and Spatie Data patterns when implementing module features.
