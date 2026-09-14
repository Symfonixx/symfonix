# ERP/CRM Testing Suite

This suite is organized by module and test type:

- `tests/Unit/Finance` - Tax calculation and finance-related business logic.
- `tests/Unit/Marketing` - WhatsApp campaign parsing/normalization logic.
- `tests/Unit/Sales` - Core model scopes, helpers, and derived attributes.
- `tests/Unit/SystemSettings` - Fingerprint and system configuration services.
- `tests/Feature/Finance` - Invoice lifecycle integration with ledger posting.
- `tests/Feature/Marketing` - WhatsApp campaign creation and queue dispatch.
- `tests/Feature/Sales` - Deal stage transitions and forecast snapshots.
- `tests/Feature/IT` - Fingerprint connectivity endpoint behavior.
- `tests/Feature/SystemSettings` - Route catalog coverage and granular RBAC enforcement.
- `tests/e2e` - Browser E2E coverage (Playwright) for admin UI workflows.

## Backend test execution

```bash
php artisan test
```

## Frontend E2E execution (Playwright)

Install dependencies:

```bash
npm install
npx playwright install
```

Required environment variables:

- `E2E_BASE_URL` (for example `http://127.0.0.1:8000`)
- `E2E_ADMIN_EMAIL`
- `E2E_ADMIN_PASSWORD`

Run:

```bash
npm run test:e2e
```

## Notes

- Tests use isolated transactions/migrations via Laravel testing helpers.
- Permissions are created at runtime per test case and cleared through Spatie's permission registrar.
- External integrations are mocked/faked where applicable (`Queue::fake`, mocked fingerprint service).
