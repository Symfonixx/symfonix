# Symfonix Business Suite

Symfonix Business Suite is a modular Laravel business platform for agencies and service companies. It combines a multilingual public website, admin panel, CRM, project delivery, finance, tax, HR, support, reporting, and product catalog in one codebase.

Built on **Laravel 13** with **nwidart/laravel-modules**, **Spatie Permission**, **Inertia + Vue 3**, **Livewire**, and the **Metronic** admin theme.

## Stack

| Layer | Tech |
|-------|------|
| Backend | PHP 8.3+, Laravel 13, modular architecture |
| Admin UI | Metronic, Bootstrap 5, Blade, Inertia/Vue, Livewire |
| Auth | Laravel Fortify (including 2FA) |
| Roles | Spatie Laravel Permission (granular catalog: `group.tab.action`) |
| i18n | English, Arabic, German, Turkish (`mcamara/laravel-localization`) |
| Frontend build | Vite, Vue 3, Tailwind (where used) |
| Extras | BotMan chatbot, visitor tracking, DomPDF, Excel export, Telescope, Pulse, WhatsApp Cloud API, ZKTeco fingerprint attendance |

## Modules

| Module | Purpose |
|--------|---------|
| **Core** | `app:install` command, shared services, helpers |
| **Base** | Settings, countries, branches, SEO, integrations, backups, `humans.txt` |
| **User** | Users, employees, roles & permissions, leave, fingerprint attendance, client portal |
| **Cms** | Pages, blog, FAQs, client logos |
| **Services** | Service categories and offerings |
| **CRM** | Leads, deals, companies, contacts, pipeline, quotes, subscriptions, activities, sales targets, email/WhatsApp marketing, sales forecasts, customizable dashboard |
| **Project** | Projects, statuses, use cases |
| **Product** | Product catalog and sales |
| **Finance** | Multi-currency ledger, invoices, accounts receivable, journal entries, salaries, commissions, expenses, product sales, exchange-rate sync |
| **Tax** | Tax rates, output/input tax ledger, filing reports |
| **Reporting** | Cross-department Finance, Sales, Marketing, Operations, and Employee reports with CSV/PDF export |
| **Support** | Tickets, subscribers, visitors |
| **Team** | Public team member profiles |
| **Testimonial** | Client testimonials |
| **SearchEngine** | Search keyword tracking |

## Requirements

- PHP 8.3+
- Composer
- Node.js 18+ and npm
- MySQL 8+ (or MariaDB)
- Redis (optional; queues/cache default to database)

## Installation

### 1. Clone and configure environment

```bash
cp .env.example .env
```

Set your database credentials in `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=symfonix
DB_USERNAME=root
DB_PASSWORD=
```

Configure finance currency context and Fixer integration:

```env
FINANCE_DEFAULT_CURRENCY=USD
FINANCE_SUPPORTED_CURRENCIES=USD,EUR,GBP,TRY
FIXER_API_KEY=
FIXER_BASE_URL=https://data.fixer.io/api
```

The default currency is the accounting base currency. Individual transactions, invoices, projects, deals, subscriptions, and products may use any configured supported currency. Administrators can set the default currency and Fixer key under **System Configurations → Finance**; database settings override the matching environment values. Each user can select a display currency from the admin header without changing stored transaction currencies.

Optional integrations (database values under **APIs & Integrations** and **System Configurations** override `.env`):

```env
# Built-in documentation site at /docs
DOCS_ENABLED=true

# WhatsApp Cloud API (Meta). Prefer APIs & Integrations → WhatsApp.
WHATSAPP_API_TOKEN=
WHATSAPP_PHONE_NUMBER_ID=
WHATSAPP_BUSINESS_ACCOUNT_ID=
WHATSAPP_API_VERSION=v21.0
WHATSAPP_WEBHOOK_VERIFY_TOKEN=

# ZKTeco fingerprint terminal. Prefer System Configurations → Fingerprint.
FINGERPRINT_ENABLED=false
FINGERPRINT_HOST=
FINGERPRINT_PORT=4370
FINGERPRINT_COMM_KEY=0
FINGERPRINT_TIMEOUT=10
FINGERPRINT_NAME_ENCODING=UTF-8
```

### 2. Install dependencies

```bash
composer install
npm install && npm run build
```

### 3. Run the install command

The install command runs migrations, seeds countries, permissions, CRM pipeline stages, support ticket categories, and creates the admin user:

```bash
php artisan app:install
```

Custom admin credentials:

```bash
php artisan app:install --email=you@company.com --password=secret --name="Your Name" --mobile=1234567890
```

Fresh reinstall (drops all tables):

```bash
php artisan app:install --fresh
```

Default credentials (if options are omitted): `admin@symfonix.com` / `password`.

### 4. Start the application

```bash
php artisan serve
```

Visit `/admin` and sign in with the credentials shown after install. Built-in HTML documentation is served at `/docs` when `DOCS_ENABLED=true`.

For queued work (marketing emails, WhatsApp campaigns, notifications, exchange-rate fetches):

```bash
php artisan queue:work
```

## What `app:install` does

1. Runs migrations (`migrate`, or `migrate:fresh` with `--fresh`)
2. Generates `APP_KEY` if missing
3. Seeds countries from `Modules/Core/database/db.sql`
4. Synchronizes the granular permission catalog (`group.tab.action`)
5. Seeds practical role scenarios (HR Manager, Sales Manager, Finance Manager, Project Manager, Operations Manager)
6. Seeds default CRM pipeline stages (Lead → Closed Won/Lost)
7. Seeds support ticket categories
8. Seeds currency settings and baseline USD/EUR/GBP/TRY exchange rates
9. Creates the **Admin** role with every permission
10. Creates the admin user and assigns the Admin role

## CRM highlights

- **Pipeline & deals** — Kanban stages, activity timeline, assignee scoping (`sales.deals.view_all` to see every deal).
- **Quotes** — line-item proposals with tax/discount, PDF download, and a public accept/reject link for customers.
- **Marketing** — queued email campaigns plus Meta WhatsApp template campaigns with recipient deduplication and delivery logs.
- **Sales forecasts** — probability-weighted pipeline projections by stage, rep, and expected close date.
- **Dashboard** — filterable analytics with a user-customizable widget layout.
- **Client portal** — customers can view assigned quotes and related records.

## Multi-currency operations

Exchange rates are stored as “1 unit of base currency equals N units of target currency.” Financial postings snapshot their source-to-base rate and base amount so later rate updates do not rewrite the original posting.

```bash
# Fetch rates asynchronously (requires a queue worker)
php artisan finance:fetch-exchange-rates

# Fetch immediately
php artisan finance:fetch-exchange-rates --sync

# Override the requested base currency
php artisan finance:fetch-exchange-rates --base=EUR --sync
```

Rates are refreshed hourly by the Laravel scheduler. Free Fixer plans are EUR-based; the application derives other base currencies through EUR cross-rates. Rate lookups are cached for 15 minutes and invalidated after synchronization or currency-setting changes.

> **Production warning:** The seeded rates are approximate. If no Fixer key or stored rate is available, the application can fall back to a `1.0` rate; this is useful for setup/demo data but is not financially accurate. Configure and monitor rate synchronization before posting production transactions. Do not change the default currency after postings exist without a controlled data migration, because historical base amounts retain the original accounting base.

## Tax

The Tax module stores inclusive/exclusive rates (optionally per region), applies them on invoice and product-sale lines, and posts **output tax** (collected) and **input tax** (paid on expenses) to a ledger. Filing reports summarize net tax payable for a date range.

## Reporting

The Reporting hub exposes Finance, Sales, Marketing, Operations, and Employee analytics with period filters and CSV/PDF export. Marketing reports include WhatsApp delivery rates; operations reports include ticket SLA and fingerprint attendance.

## Permissions

Permissions are managed through `php artisan app:install` on fresh setups (and remapped from legacy names such as `CRM Management` when roles already exist). The catalog uses `group.tab.action` keys, for example:

| Group | Example keys |
|-------|----------------|
| Overview | `overview.dashboard.view`, `overview.crm_analytics.view` |
| CMS | `cms.pages.*`, `cms.blogs.*`, `cms.clients.*` |
| CRM | `crm.leads.*`, `crm.companies.*`, `crm.activities.*` |
| Sales | `sales.deals.view_all`, `sales.quotes.*`, `sales.forecasts.view` |
| Marketing | `marketing.email.send`, `marketing.whatsapp.send`, `marketing.whatsapp_templates.*` |
| Finance | `finance.invoices.*`, `finance.ar.view`, `finance.salaries.*` |
| Tax | `tax.rates.*`, `tax.ledger.view`, `tax.filing.export` |
| Reporting | `reporting.finance.view`, `reporting.sales.export` |
| HR | `hr.employees.*`, `hr.fingerprint.manage`, `hr.roles.*` |
| Settings | `settings.system.*`, `settings.integrations.*`, `settings.backups.*` |

Assign permissions to roles in the admin panel under **User Management → Roles**. Install seeds **Admin** plus the scenario roles listed above. Extra actions include `send`, `export`, `approve`, `view_all`, `manage`, `reply`, and `restore`.

## Development

```bash
# Run migrations after pulling schema changes
php artisan migrate

# Frontend dev server
npm run dev

# Queue worker
php artisan queue:work

# Backend tests
php artisan test

# Playwright E2E (requires a running app and E2E_* env vars — see tests/README.md)
npm run test:e2e

# Clear caches
php artisan optimize:clear
```

### Optional tooling

- **Telescope** — `php artisan telescope:install` (debugging)
- **Pulse** — performance monitoring (tables created by migration)
- **Chatbot** — BotMan web widget (optional Ollama integration)
- **Docs** — static HTML at `/docs` (`DOCS_ENABLED=false` to disable)

## Deployment notes

For production:

1. Set `APP_ENV=production`, `APP_DEBUG=false`
2. Run `composer install --no-dev --optimize-autoloader`
3. Run `npm ci && npm run build`
4. Run `php artisan app:install` on a fresh database, or `php artisan migrate --force` on an existing one
5. Run `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`
6. Configure a queue worker and scheduler (`php artisan schedule:run` via cron); the scheduler refreshes exchange rates hourly; WhatsApp and email campaigns require the worker
7. Configure a valid Fixer key and monitor the last successful fetch under **System Configurations → Finance**
8. Set WhatsApp Cloud API credentials under **APIs & Integrations** if you send template campaigns
9. Set `DOCS_ENABLED=false` if you do not want to expose `/docs`

## Key packages

- [laravel-modules](https://github.com/nWidart/laravel-modules) — modular architecture
- [laravel-permission](https://github.com/spatie/laravel-permission) — roles and permissions
- [laravel-data](https://github.com/spatie/laravel-data) — DTOs
- [laravel-translatable](https://github.com/spatie/laravel-translatable) — model translations
- [laravel-localization](https://github.com/mcamara/laravel-localization) — English, Arabic, German, Turkish
- [inertia-laravel](https://inertiajs.com) / Vue 3 — SPA-style pages
- [livewire](https://livewire.laravel.com) — reactive Blade components
- [intervention/image](https://github.com/Intervention/image) — image handling
- [Laravel Fortify](https://laravel.com/docs/fortify) — authentication with 2FA support
- [BotMan](https://botman.io) — website chatbot
- [maatwebsite/excel](https://github.com/SpartnerNL/Laravel-Excel) — import/export
- [WhatsApp Cloud API](https://developers.facebook.com/docs/whatsapp/cloud-api) — template campaigns

## License

MIT
