import { expect, test, Page } from '@playwright/test';

async function loginAs(page: Page, email: string, password: string): Promise<void> {
  await page.goto('/admin/login');
  await page.getByLabel(/email/i).fill(email);
  await page.getByLabel(/password/i).fill(password);
  await page.getByRole('button', { name: /log ?in|sign ?in/i }).click();
  await page.waitForURL(/\/admin\//);
}

test.describe('ERP/CRM Admin UI E2E', () => {
  const adminEmail = process.env.E2E_ADMIN_EMAIL;
  const adminPassword = process.env.E2E_ADMIN_PASSWORD;

  test.beforeEach(async ({ page }) => {
    test.skip(!adminEmail || !adminPassword, 'Set E2E admin credentials to run browser E2E tests.');
    await loginAs(page, adminEmail as string, adminPassword as string);
  });

  test('renders role-aware sidebar navigation', async ({ page }) => {
    await page.goto('/admin/dashboard');
    await expect(page.locator('aside')).toBeVisible();
    await expect(page.getByRole('link', { name: /finance/i })).toBeVisible();
    await expect(page.getByRole('link', { name: /crm|sales/i })).toBeVisible();
  });

  test('supports WhatsApp template dynamic form builder flow', async ({ page }) => {
    await page.goto('/admin/crm/marketing/whatsapp-templates/create');

    await expect(page.locator('form')).toBeVisible();
    await page.locator('input[name="name"]').fill(`autotest-template-${Date.now()}`);
    await page.locator('textarea[name="body"]').fill('Hello {{1}}, your order {{2}} is ready');
    await expect(page.locator('textarea[name="body"]')).toHaveValue(/{{1}}.*{{2}}/);
    await expect(page.getByRole('button', { name: /save|create/i })).toBeVisible();
  });

  test('shows table filters, date controls, and export actions', async ({ page }) => {
    await page.goto('/admin/crm/sales-forecasts');

    await expect(page.locator('form')).toBeVisible();
    await expect(page.locator('input[name*="date"], input[type="date"]')).toHaveCount(2);

    await page.goto('/admin/contact_forms');
    await expect(page.getByRole('link', { name: /export/i })).toBeVisible();

    await page.goto('/admin/finance/invoices');
    await expect(page.locator('table')).toBeVisible();
  });
});
