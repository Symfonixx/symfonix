# Symfonix

Symfonix is a modular Laravel business platform for agencies and service companies. It combines a public website, admin panel, CRM, project delivery, finance, HR, and product catalog in one codebase.

Built on **Laravel 13** with **nwidart/laravel-modules**, **Spatie Permission**, **Inertia**, and the **Metronic** admin theme.

## Modules

| Module | Purpose |
|--------|---------|
| **Core** | Install command, shared services, helpers |
| **Base** | Settings, countries, branches, SEO |
| **User** | Users, employees, roles |
| **Cms** | Pages, blog, FAQs |
| **Services** | Service categories and offerings |
| **CRM** | Leads, deals, companies, pipeline, subscriptions, activities |
| **Project** | Projects, statuses, use cases |
| **Product** | Product catalog and sales |
| **Finance** | Transactions, salaries, commissions, expenses |
| **Support** | Contact subscribers and complaints |
| **Team** | Team members |
| **Testimonial** | Client testimonials |
| **SearchEngine** | Search keyword tracking |

## Requirements

- PHP 8.3+
- Composer
- Node.js 18+ and npm
- MySQL 8+ (or MariaDB)
- Redis (optional, for queues/cache)

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

### 2. Install dependencies

```bash
composer install
npm install && npm run build
```

### 3. Run the install command

The install command runs migrations, seeds countries, permissions, CRM pipeline stages, and creates the admin user:

```bash
php artisan app:install
```

Custom admin credentials:

```bash
php artisan app:install --email=you@company.com --password=secret --name="Your Name" --mobile=1234567890
```

### 4. Start the application

```bash
php artisan serve
```

Visit `/admin` and sign in with the credentials shown after install.

## What `app:install` does

1. Generates `APP_KEY`
2. Runs all module migrations (schema only — no permission or seed data in migrations)
3. Seeds countries from `Modules/Core/database/db.sql`
4. Creates all application permissions
5. Seeds default CRM pipeline stages (Lead → Closed Won/Lost)
6. Creates the **Admin** role with every permission
7. Creates the admin user and assigns the Admin role

## Permissions

Permissions are managed through `php artisan app:install` on fresh setups, not via migrations. Available permissions:

- Settings Management
- CMS Management
- Support Management
- Hr Management
- App Monitoring
- Logs Management
- CRM Management / CRM View All
- Sales Management
- Project Management
- Finance Management
- Services Management
- Product Management
- Testimonials Management

Assign permissions to roles in the admin panel under User management.

## Development

```bash
# Run migrations after pulling schema changes
php artisan migrate

# Frontend dev server
npm run dev

# Queue worker
php artisan queue:work

# Clear caches
php artisan optimize:clear
```

### Optional tooling

- **Telescope** — `php artisan telescope:install` (debugging)
- **Pulse** — performance monitoring (tables created by migration)

## Deployment notes

For production:

1. Set `APP_ENV=production`, `APP_DEBUG=false`
2. Run `composer install --no-dev --optimize-autoloader`
3. Run `npm ci && npm run build`
4. Run `php artisan app:install` on a fresh database, or `php artisan migrate --force` on an existing one
5. Run `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`
6. Configure a queue worker and scheduler (`php artisan schedule:run` via cron)

## Key packages

- [laravel-modules](https://github.com/nWidart/laravel-modules) — modular architecture
- [laravel-permission](https://github.com/spatie/laravel-permission) — roles and permissions
- [laravel-data](https://github.com/spatie/laravel-data) — DTOs
- [laravel-localization](https://github.com/mcamara/laravel-localization) — English and Arabic
- [inertia-laravel](https://inertiajs.com) — SPA-style admin pages
- [intervention/image](https://github.com/Intervention/image) — image handling
- [Laravel Fortify](https://laravel.com/docs/fortify) — authentication with 2FA support

## License

MIT
