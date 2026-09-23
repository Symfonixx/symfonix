<?php

namespace Modules\AI\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Cms\Models\Blog;
use Modules\Cms\Models\Page;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Contact;
use Modules\CRM\Models\CrmActivity;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\MarketingCampaign;
use Modules\CRM\Models\MarketingGroup;
use Modules\CRM\Models\Quote;
use Modules\CRM\Models\Subscription;
use Modules\CRM\Models\WhatsAppCampaign;
use Modules\Finance\Models\Commission;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\Salary;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductSale;
use Modules\Project\Models\Project;
use Modules\Services\Models\Service;
use Modules\Support\Models\Ticket;
use Modules\Tax\Models\TaxLedgerEntry;
use Modules\Tax\Models\TaxRate;
use Modules\User\Models\Employee;
use Modules\User\Models\LeaveRequest;

/**
 * Allowlisted business entities Ask Symfonix may read via Eloquent.
 * Secrets, settings, auth, and internal AI tables are intentionally excluded.
 */
final class QueryableEntityRegistry
{
    /**
     * @return array<string, array{
     *     label: string,
     *     model: class-string<Model>,
     *     permissions: list<string>,
     *     searchable: list<string>,
     *     columns: list<string>,
     *     filters: list<string>,
     *     order_by: string,
     * }>
     */
    public function definitions(): array
    {
        return [
            'lead' => [
                'label' => 'Leads',
                'model' => Lead::class,
                'permissions' => ['crm.leads.view'],
                'searchable' => ['name', 'email', 'phone', 'company_name'],
                'columns' => ['id', 'name', 'email', 'phone', 'status', 'source', 'company_name', 'job_title', 'company_id', 'assigned_to', 'created_at', 'updated_at'],
                'filters' => ['status', 'source'],
                'order_by' => 'id',
            ],
            'contact' => [
                'label' => 'Contacts',
                'model' => Contact::class,
                'permissions' => ['crm.contacts.view'],
                'searchable' => ['name', 'email', 'phone', 'job_title'],
                'columns' => ['id', 'name', 'email', 'phone', 'phone2', 'source', 'job_title', 'company_id', 'is_primary', 'created_at'],
                'filters' => ['source', 'company_id'],
                'order_by' => 'id',
            ],
            'customer' => [
                'label' => 'Customers / Companies',
                'model' => Company::class,
                'permissions' => ['crm.companies.view'],
                'searchable' => ['name', 'email', 'phone', 'city', 'country'],
                'columns' => ['id', 'name', 'email', 'phone', 'status', 'activity_type', 'country', 'city', 'address', 'created_at'],
                'filters' => ['status', 'activity_type'],
                'order_by' => 'id',
            ],
            'deal' => [
                'label' => 'Deals',
                'model' => Deal::class,
                'permissions' => ['sales.deals.view'],
                'searchable' => ['title', 'source', 'description'],
                'columns' => ['id', 'title', 'company_id', 'lead_id', 'pipeline_stage_id', 'assigned_to', 'value', 'currency', 'probability', 'expected_close_date', 'source', 'status', 'won_at', 'lost_at', 'closed_at', 'created_at'],
                'filters' => ['status', 'company_id', 'assigned_to'],
                'order_by' => 'id',
            ],
            'quote' => [
                'label' => 'Quotes',
                'model' => Quote::class,
                'permissions' => ['sales.quotes.view'],
                'searchable' => ['quote_number', 'notes'],
                'columns' => ['id', 'quote_number', 'company_id', 'deal_id', 'project_id', 'status', 'subtotal', 'discount_amount', 'tax_amount', 'total', 'currency', 'issued_at', 'expires_at', 'created_at'],
                'filters' => ['status', 'company_id'],
                'order_by' => 'id',
            ],
            'subscription' => [
                'label' => 'Subscriptions',
                'model' => Subscription::class,
                'permissions' => ['sales.subscriptions.view'],
                'searchable' => ['name', 'notes'],
                'columns' => ['id', 'name', 'company_id', 'service_id', 'status', 'billing_cycle', 'amount', 'currency', 'starts_at', 'ends_at', 'renewal_at', 'auto_renew', 'created_at'],
                'filters' => ['status', 'billing_cycle', 'company_id'],
                'order_by' => 'id',
            ],
            'email_campaign' => [
                'label' => 'Email Campaigns',
                'model' => MarketingCampaign::class,
                'permissions' => ['marketing.email.view'],
                'searchable' => ['subject'],
                'columns' => ['id', 'subject', 'marketing_group_id', 'user_id', 'recipients_count', 'status', 'created_at', 'updated_at'],
                'filters' => ['status', 'marketing_group_id'],
                'order_by' => 'id',
            ],
            'whatsapp_campaign' => [
                'label' => 'WhatsApp Campaigns',
                'model' => WhatsAppCampaign::class,
                'permissions' => ['marketing.whatsapp.view'],
                'searchable' => ['rendered_preview'],
                'columns' => ['id', 'marketing_group_id', 'whatsapp_template_id', 'user_id', 'recipients_count', 'status', 'created_at', 'updated_at'],
                'filters' => ['status', 'marketing_group_id'],
                'order_by' => 'id',
            ],
            'marketing_group' => [
                'label' => 'Marketing Groups',
                'model' => MarketingGroup::class,
                'permissions' => ['marketing.email.view', 'marketing.whatsapp.view'],
                'searchable' => ['title', 'goal'],
                'columns' => ['id', 'title', 'goal', 'user_id', 'created_at'],
                'filters' => [],
                'order_by' => 'id',
            ],
            'activity' => [
                'label' => 'CRM Activities / Tasks',
                'model' => CrmActivity::class,
                'permissions' => ['crm.activities.view'],
                'searchable' => ['title', 'body'],
                'columns' => ['id', 'subject_type', 'subject_id', 'type', 'title', 'scheduled_at', 'completed_at', 'user_id', 'created_at'],
                'filters' => ['type', 'user_id'],
                'order_by' => 'id',
            ],
            'project' => [
                'label' => 'Projects',
                'model' => Project::class,
                'permissions' => ['project.projects.view'],
                'searchable' => ['title', 'description'],
                'columns' => ['id', 'title', 'company_id', 'project_status_id', 'deal_id', 'budget', 'currency', 'payment_status', 'start_date', 'due_date', 'created_at'],
                'filters' => ['payment_status', 'company_id', 'project_status_id'],
                'order_by' => 'id',
            ],
            'invoice' => [
                'label' => 'Invoices',
                'model' => Invoice::class,
                'permissions' => ['finance.invoices.view'],
                'searchable' => ['invoice_number', 'notes'],
                'columns' => ['id', 'invoice_number', 'company_id', 'deal_id', 'project_id', 'subscription_id', 'status', 'subtotal', 'tax_amount', 'total', 'currency', 'issued_at', 'due_at', 'paid_at', 'created_at'],
                'filters' => ['status', 'company_id'],
                'order_by' => 'id',
            ],
            'journal_entry' => [
                'label' => 'Journal Entries (Payments / Expenses)',
                'model' => JournalEntry::class,
                'permissions' => ['finance.dashboard.view', 'finance.daily_log.view'],
                'searchable' => ['description'],
                'columns' => ['id', 'flow', 'amount', 'currency', 'base_amount', 'description', 'transaction_date', 'reference_type', 'reference_id', 'tax_amount', 'created_at'],
                'filters' => ['flow'],
                'order_by' => 'id',
            ],
            'product' => [
                'label' => 'Products',
                'model' => Product::class,
                'permissions' => ['product.catalog.view'],
                'searchable' => ['name', 'sku', 'slug'],
                'columns' => ['id', 'name', 'slug', 'sku', 'product_category_id', 'status', 'billing_type', 'price', 'currency', 'created_at'],
                'filters' => ['status', 'billing_type'],
                'order_by' => 'id',
            ],
            'product_sale' => [
                'label' => 'Product Sales',
                'model' => ProductSale::class,
                'permissions' => ['finance.product_sales.view'],
                'searchable' => ['notes'],
                'columns' => ['id', 'product_id', 'company_id', 'invoice_id', 'deal_id', 'quantity', 'unit_price', 'tax_amount', 'total_amount', 'currency', 'sold_at', 'created_at'],
                'filters' => ['product_id', 'company_id'],
                'order_by' => 'id',
            ],
            'service' => [
                'label' => 'Services',
                'model' => Service::class,
                'permissions' => ['services.catalog.view'],
                'searchable' => ['title', 'slug'],
                'columns' => ['id', 'title', 'slug', 'service_category_id', 'status', 'featured', 'visits', 'created_at'],
                'filters' => ['status'],
                'order_by' => 'id',
            ],
            'employee' => [
                'label' => 'Employees',
                'model' => Employee::class,
                'permissions' => ['hr.employees.view'],
                'searchable' => ['name', 'email', 'mobile', 'position'],
                'columns' => ['id', 'name', 'email', 'mobile', 'position', 'status', 'user_id', 'created_at'],
                'filters' => ['status'],
                'order_by' => 'id',
            ],
            'leave' => [
                'label' => 'Leave Requests',
                'model' => LeaveRequest::class,
                'permissions' => ['hr.leaves.view'],
                'searchable' => ['reason', 'manager_note'],
                'columns' => ['id', 'employee_id', 'type', 'start_date', 'end_date', 'status', 'reason', 'created_at'],
                'filters' => ['status', 'type', 'employee_id'],
                'order_by' => 'id',
            ],
            'salary' => [
                'label' => 'Salaries',
                'model' => Salary::class,
                'permissions' => ['finance.salaries.view'],
                'searchable' => [],
                'columns' => ['id', 'employee_id', 'base_salary', 'period', 'paid_at', 'status', 'created_at'],
                'filters' => ['status', 'employee_id'],
                'order_by' => 'id',
            ],
            'commission' => [
                'label' => 'Commissions',
                'model' => Commission::class,
                'permissions' => ['finance.commissions.view'],
                'searchable' => [],
                'columns' => ['id', 'deal_id', 'employee_id', 'commission_percentage', 'commission_amount', 'status', 'created_at'],
                'filters' => ['status', 'employee_id'],
                'order_by' => 'id',
            ],
            'ticket' => [
                'label' => 'Support Tickets',
                'model' => Ticket::class,
                'permissions' => ['support.tickets.view'],
                'searchable' => ['ticket_number', 'subject', 'description'],
                'columns' => ['id', 'ticket_number', 'user_id', 'ticket_category_id', 'assigned_to', 'subject', 'priority', 'status', 'closed_at', 'created_at'],
                'filters' => ['status', 'priority', 'assigned_to'],
                'order_by' => 'id',
            ],
            'tax_rate' => [
                'label' => 'Tax Rates',
                'model' => TaxRate::class,
                'permissions' => ['tax.rates.view'],
                'searchable' => ['name', 'region_code'],
                'columns' => ['id', 'name', 'percentage', 'type', 'region_code', 'status', 'is_default', 'created_at'],
                'filters' => ['status', 'type'],
                'order_by' => 'id',
            ],
            'tax_ledger' => [
                'label' => 'Tax Ledger',
                'model' => TaxLedgerEntry::class,
                'permissions' => ['tax.ledger.view'],
                'searchable' => ['description'],
                'columns' => ['id', 'tax_rate_id', 'direction', 'amount', 'currency', 'base_amount', 'transaction_date', 'company_id', 'project_id', 'description', 'created_at'],
                'filters' => ['direction', 'company_id'],
                'order_by' => 'id',
            ],
            'page' => [
                'label' => 'CMS Pages',
                'model' => Page::class,
                'permissions' => ['cms.pages.view'],
                'searchable' => ['title', 'slug'],
                'columns' => ['id', 'title', 'slug', 'status', 'featured', 'visits', 'created_at'],
                'filters' => ['status'],
                'order_by' => 'id',
            ],
            'blog' => [
                'label' => 'CMS Blogs',
                'model' => Blog::class,
                'permissions' => ['cms.blogs.view'],
                'searchable' => ['title', 'slug'],
                'columns' => ['id', 'title', 'slug', 'category_id', 'status', 'featured', 'visits', 'created_at'],
                'filters' => ['status'],
                'order_by' => 'id',
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public function keys(): array
    {
        return array_keys($this->definitions());
    }

    /**
     * @return array{label: string, model: class-string<Model>, permissions: list<string>, searchable: list<string>, columns: list<string>, filters: list<string>, order_by: string}|null
     */
    public function get(string $key): ?array
    {
        return $this->definitions()[$key] ?? null;
    }

    public function authorized(User $user, string $key): bool
    {
        $definition = $this->get($key);
        if ($definition === null) {
            return false;
        }

        return $user->canany($definition['permissions']);
    }

    /**
     * @return list<array{key: string, label: string, searchable: list<string>, filters: list<string>}>
     */
    public function authorizedCatalog(User $user): array
    {
        $catalog = [];

        foreach ($this->definitions() as $key => $definition) {
            if (! $user->canany($definition['permissions'])) {
                continue;
            }

            $catalog[] = [
                'key' => $key,
                'label' => $definition['label'],
                'searchable' => $definition['searchable'],
                'filters' => $definition['filters'],
            ];
        }

        return $catalog;
    }

    /**
     * @param  array{label: string, model: class-string<Model>, permissions: list<string>, searchable: list<string>, columns: list<string>, filters: list<string>, order_by: string}  $definition
     * @param  array<string, mixed>  $filters
     */
    public function applyFilters(Builder $query, array $definition, array $filters): void
    {
        $allowed = $definition['filters'];

        foreach ($filters as $column => $value) {
            if (! is_string($column) || ! in_array($column, $allowed, true)) {
                continue;
            }

            if ($value === null || $value === '') {
                continue;
            }

            if (is_scalar($value)) {
                $query->where($column, $value);
            }
        }
    }

    /**
     * @param  array{label: string, model: class-string<Model>, permissions: list<string>, searchable: list<string>, columns: list<string>, filters: list<string>, order_by: string}  $definition
     */
    public function applySearch(Builder $query, array $definition, string $search): void
    {
        $columns = $definition['searchable'];
        if ($columns === [] || trim($search) === '') {
            return;
        }

        $like = '%'.trim($search).'%';

        $query->where(function (Builder $builder) use ($columns, $like): void {
            foreach ($columns as $index => $column) {
                if ($index === 0) {
                    $builder->where($column, 'like', $like);
                } else {
                    $builder->orWhere($column, 'like', $like);
                }
            }
        });
    }

    /**
     * @param  array{label: string, model: class-string<Model>, permissions: list<string>, searchable: list<string>, columns: list<string>, filters: list<string>, order_by: string}  $definition
     * @return array<string, mixed>
     */
    public function serializeModel(Model $model, array $definition, CostLimiter $limiter): array
    {
        $row = [];

        foreach ($definition['columns'] as $column) {
            $value = $model->getAttribute($column);

            if ($value instanceof \DateTimeInterface) {
                $row[$column] = $value->format('Y-m-d H:i:s');
            } elseif (is_string($value)) {
                $row[$column] = $limiter->truncateString($value);
            } elseif (is_array($value)) {
                $row[$column] = $limiter->truncate($value);
            } else {
                $row[$column] = $value;
            }
        }

        return $row;
    }
}
