# Symfonix Business Suite

Symfonix Business Suite is a modular Laravel business platform for agencies and service companies. It combines a multilingual public website, admin panel, CRM, project delivery, finance, tax, HR, support, reporting, product catalog, and an AI layer (Ask Symfonix, content generation, image editing, and the public chatbot) in one codebase.

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
| AI | OpenAI and Google Gemini (Ask Symfonix, TinyMCE/form generation, Gemini image edit, public BotMan chatbot) |
| Extras | BotMan chatbot, visitor tracking, DomPDF, Excel export, Telescope, Pulse, WhatsApp Cloud API, ZKTeco fingerprint attendance |

## Modules

| Module | Purpose |
|--------|---------|
| **Core** | `app:install` command, shared services, helpers |
| **Base** | Settings, countries, branches, SEO, integrations (SMTP, WhatsApp, OpenAI, Gemini), backups, `humans.txt` |
| **AI** | Ask Symfonix admin assistant, public website chatbot, form/quote/follow-up generation, Gemini image create/edit |
| **User** | Users, employees, roles & permissions, leave, fingerprint attendance, client portal |
| **Cms** | Pages, blog, FAQs, client logos (AI form fill and image edit on pages/posts) |
| **Services** | Service categories and offerings (AI form fill; catalog used by the public chatbot) |
| **CRM** | Leads, deals, companies, contacts, pipeline, quotes, subscriptions, activities, sales targets, email/WhatsApp marketing, sales forecasts, customizable dashboard, AI quote and lead follow-up drafts |
| **Project** | Projects, statuses, use cases (AI case-study copy and image edit) |
| **Product** | Product catalog and sales (AI product-page copy and image edit) |
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
- OpenAI and/or Gemini API keys (optional; required to enable AI features)

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

# AI providers. Prefer APIs & Integrations → AI Integrations.
GEMINI_API_KEY=
GEMINI_IMAGE_MODEL=gemini-2.5-flash-image
GEMINI_ANALYSIS_MODEL=gemini-2.5-flash
OPENAI_API_KEY=
OPENAI_MODEL=gpt-4o-mini
AI_ASSISTANT_PROVIDER=auto
AI_CHATBOT_ENABLED=true

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
4. Synchronizes the granular permission catalog (`group.tab.action`), including `ai.assistant.view`
5. Seeds practical role scenarios (HR Manager, Sales Manager, Finance Manager, Project Manager, Operations Manager)
6. Seeds default CRM pipeline stages (Lead → Closed Won/Lost)
7. Seeds support ticket categories
8. Seeds currency settings and baseline USD/EUR/GBP/TRY exchange rates
9. Creates the **Admin** role with every permission
10. Creates the admin user and assigns the Admin role

## AI (Ask Symfonix, generation, chatbot)

The **AI** module is optional at runtime: the rest of the suite works without API keys. When OpenAI and/or Gemini are configured under **APIs & Integrations → AI Integrations**, four capabilities turn on. Saved database credentials override `.env`.

| Capability | Where it appears | Provider |
|------------|------------------|----------|
| **Ask Symfonix** | Admin header drawer and dashboard suggestion chips | `AI_ASSISTANT_PROVIDER` (`auto`, `openai`, or `gemini`) |
| **Content generation** | TinyMCE “Create with AI”, form fill on CMS/product/service/use-case screens | OpenAI first, Gemini analysis model as fallback |
| **Quote & follow-up drafts** | Quote create form; lead show page | Same as content generation |
| **Image create/edit** | “Edit with AI” / “Create with AI” on product, page, blog, and use-case images | Gemini image model only |
| **Public chatbot** | BotMan widget on the public site | Same router as Ask Symfonix; catalog fallback if AI is off |

### Ask Symfonix

Ask Symfonix is a **read-only** business assistant. Staff with `ai.assistant.view` can ask questions in the admin language they are using; the model must call tools and is instructed not to invent numbers, names, or statuses.

- Conversations and messages are stored per user (`ai_conversations`, `ai_messages`).
- Each tool is gated by the same Spatie permissions as the matching admin screen. A sales user cannot pull payroll data they cannot already see.
- `auto` tries OpenAI first, then Gemini. Content generation and image editing keep their own provider rules.
- Typical questions: overdue invoices, who has overdue tasks, this month’s sales, year-over-year growth, top customers, leads to follow today, website visitors, best-selling services, employee utilization.

Tool coverage includes snapshot/today focus, leads, customers, projects, invoices, payments, expenses, employees, tickets, overdue work, visitors, best-selling services, record search, and follow-up drafts. The assistant never claims it created records, sent email, or sent WhatsApp.

### Content, quotes, and follow-ups

- **Editor HTML** — generate or rewrite TinyMCE body copy from a prompt (and optional existing HTML).
- **Form fill** — structured JSON for `cms_blog`, `cms_page`, `service`, `product`, and `use_case` (titles, slugs, SEO, body). Copy is localized; slugs stay English kebab-case.
- **Quotes** — from a company + deal, using published services/products (or deal lines) as the catalog. Fills quote fields and line items for review before save. Requires `sales.quotes.create`.
- **Lead follow-up** — drafts an activity (type, title, body, scheduled time) from lead context. Requires `crm.activities.create`. The user still saves the activity.

### Image editing

Gemini can **edit** an existing image or **create** one from a prompt. Optional brand-logo matching uses the logo from **Settings → Branding**. Allowed targets are registered in `Modules/AI/config/config.php` (`product`, `cms_page`, `cms_blog`, `use_case`) and each write still requires the matching edit permission (`product.catalog.edit`, `cms.pages.edit`, and so on).

### Public website chatbot

The BotMan widget uses the same OpenAI/Gemini keys, with a **public-safe** tool set: list/get published services, company profile, capture a website lead (name + valid email), and suggest quick-reply buttons. It does not expose CRM, finance, or HR data. Traffic is rate-limited per IP (`AI_CHATBOT_RATE_LIMIT`, default 20 / 60s). If AI is disabled or unconfigured, the widget still answers from the published service catalog.

Disable the AI chatbot with `AI_CHATBOT_ENABLED=false`.

## Sales & delivery lifecycle

The main commercial path is **inquiry → lead → deal → (quote) → closed won → project**. Case studies and subscriptions sit beside that path; they are not created automatically.

```
Website contact form ──staff convert──► Inquiry (ContactForm) ──Convert to Lead──► Lead
Website chatbot ──────────────────────────────────────────────► Lead (source: website)
Admin (manual) ───────────────────────────────────────────────► Lead

Lead ──Convert──► Company + Contact + Deal
Lead ──Convert to customer──► Company + Contact (no deal)

Deal pipeline (seeded):
  Lead (10%) → Qualified (25%) → Proposal (50%) → Negotiation (75%)
    → Closed Won (100%) ──auto──► Project (Planning) + income journal
    → Closed Lost (0%)

Deal ──Create Quote──► Quote (draft → sent) ──customer Accept──► Closed Won + Project
Won deal ──Create Invoice──► Invoice (manual; not created from the quote)
Company ──Subscriptions──► recurring invoices (parallel to deals)
Completed project ──author──► Use case / case study on /use-cases
```

| Step | What happens | Who triggers it |
|------|----------------|-----------------|
| **Inquiry** | Public `/contact-us` stores a contact form (name, email, message). It does **not** create a lead by itself. | Visitor |
| **Lead (chat)** | The public chatbot can create a lead directly (`source = website`) after name + valid email. | Visitor + AI |
| **Inquiry → Lead** | Admin **Convert to Lead** copies the inquiry into a lead and can also **Convert to Contact** only. | Staff |
| **Lead → Deal** | Admin **Convert** on the lead creates or finds a **company** and **primary contact**, then a **deal** on the default pipeline stage (usually Lead), copying budget and services. | Staff |
| **Lead → Customer** | Admin **Convert to customer** provisions company + contact without a deal. | Staff |
| **Pipeline** | Kanban or deal show **Move Stage**. Won sets `won` / `won_at`; lost sets `lost`. | Staff |
| **Closed Won** | A listener creates a **project** (idempotent, starts at **Planning**) and posts deal income + pending commission. | Automatic |
| **Quote** | **Create Quote** from a deal copies service lines (`draft` → **Mark sent**). The customer (portal login) **accepts** or **rejects** on the public link. Accept moves the deal to Closed Won and creates/links the project. Reject leaves the deal unchanged. | Staff + customer |
| **Invoice** | **Create Invoice from Deal** on a won deal. Quotes do not auto-invoice. | Staff |
| **Activities** | Timeline notes/calls/meetings/tasks on lead, deal, company, contact. AI can **draft** a lead follow-up; staff still save it. | Staff |
| **Subscription** | Manual on a company. First invoice on create; renewals via `finance:process-subscription-renewals`. No `deal_id`. | Staff |

Do not convert a lead **straight onto Closed Won** in the convert modal if you need the project and finance listeners: those run on a **stage change** event, which is not fired on initial deal create.

## Projects & case studies

Winning a deal creates a **project** for delivery (statuses: Planning → Development → QA → Completed → On Hold). Projects are operational records (budget, services, dates, company).

**Use cases** (case studies) are a separate public-marketing record (`/use-cases` and homepage featured cards). Completing a project does **not** auto-publish a case study. In admin, create a use case under **Project → Use Cases**, optionally **link the project**, then publish. AI can fill the case-study form (challenge, solution, results, HTML body) and edit the image. Testimonials on completed projects (client portal) are another public surface, also not automatic.

## CRM highlights

- **Pipeline & deals** — Kanban stages, activity timeline, assignee scoping (`sales.deals.view_all` to see every deal).
- **Quotes** — line-item proposals with tax/discount, PDF download, a public accept/reject link, and optional AI draft from the linked company and deal.
- **Marketing** — queued email campaigns plus Meta WhatsApp template campaigns with recipient deduplication and delivery logs.
- **Sales forecasts** — probability-weighted pipeline projections by stage, rep, and expected close date.
- **Dashboard** — filterable analytics with a user-customizable widget layout.
- **Client portal** — customers can view assigned quotes, subscriptions, and related records.
- **AI follow-ups** — on a lead record, generate a suggested next activity instead of writing it from scratch.

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
| AI | `ai.assistant.view` (Ask Symfonix). Content/image/quote/follow-up reuse the matching create/edit keys |
| CMS | `cms.pages.*`, `cms.blogs.*`, `cms.clients.*` |
| CRM | `crm.leads.*`, `crm.companies.*`, `crm.activities.*` |
| Sales | `sales.deals.view_all`, `sales.quotes.*`, `sales.forecasts.view` |
| Marketing | `marketing.email.send`, `marketing.whatsapp.send`, `marketing.whatsapp_templates.*` |
| Finance | `finance.invoices.*`, `finance.ar.view`, `finance.salaries.*` |
| Tax | `tax.rates.*`, `tax.ledger.view`, `tax.filing.export` |
| Reporting | `reporting.finance.view`, `reporting.sales.export` |
| Project | `project.projects.*`, `project.use_cases.*` |
| HR | `hr.employees.*`, `hr.fingerprint.manage`, `hr.roles.*` |
| Settings | `settings.system.*`, `settings.integrations.*`, `settings.backups.*` |

Assign permissions to roles in the admin panel under **User Management → Roles**. Install seeds **Admin** plus the scenario roles listed above. Extra actions include `send`, `export`, `approve`, `view_all`, `manage`, `reply`, and `restore`.

Ask Symfonix tools call `canany()` on the same keys as the screens they summarize. Configure OpenAI/Gemini under **APIs & Integrations** (`settings.integrations.*`).

## Development

```bash
# Run migrations after pulling schema changes
php artisan migrate

# Frontend dev server
npm run dev

# Queue worker
php artisan queue:work

# Backend tests (uses dedicated `symfonix_testing` database — see tests/README.md)
php artisan test

# Playwright E2E (requires a running app and E2E_* env vars — see tests/README.md)
npm run test:e2e

# Clear caches
php artisan optimize:clear
```

### Optional tooling

- **Telescope** — `php artisan telescope:install` (debugging)
- **Pulse** — performance monitoring (tables created by migration)
- **Chatbot** — BotMan web widget powered by OpenAI/Gemini (same keys as Ask Symfonix), with a structured lead-capture fallback
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
9. Set OpenAI and/or Gemini keys under **APIs & Integrations → AI Integrations** if you use Ask Symfonix, content generation, image editing, or the public AI chatbot. Restrict `ai.assistant.view` to staff who should query live business data.
10. Set `DOCS_ENABLED=false` if you do not want to expose `/docs`

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
- [OpenAI API](https://platform.openai.com/docs) — Ask Symfonix and content generation
- [Google Gemini](https://ai.google.dev/gemini-api/docs) — Ask Symfonix fallback, analysis, and image create/edit
- [maatwebsite/excel](https://github.com/SpartnerNL/Laravel-Excel) — import/export
- [WhatsApp Cloud API](https://developers.facebook.com/docs/whatsapp/cloud-api) — template campaigns

## License

MIT
