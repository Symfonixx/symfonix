<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Modules\AI\Support\ToolResult;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Deal;
use Modules\Finance\Models\Invoice;
use Modules\Project\Models\Project;

class GetCustomerDetailsTool extends AbstractAssistantTool
{
    public function name(): string
    {
        return 'get_customer_details';
    }

    public function description(): string
    {
        return 'Get a CRM customer (company) summary: contact info, recent deals, projects, and invoices.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'company_id' => [
                    'type' => 'integer',
                    'description' => 'Company / customer id',
                ],
            ],
            'required' => ['company_id'],
        ];
    }

    public function permissions(): array
    {
        return ['crm.companies.view'];
    }

    protected function execute(User $user, array $arguments): ToolResult
    {
        $id = (int) ($arguments['company_id'] ?? 0);
        $company = Company::query()->find($id);

        if ($company === null) {
            return ToolResult::empty(__('ai::assistant.errors.not_found'), ['Customers']);
        }

        $limit = $this->limiter->maxListItems();
        $canDeals = $user->can('sales.deals.view');
        $canProjects = $user->can('project.projects.view');
        $canInvoices = $user->canany(['finance.invoices.view', 'finance.dashboard.view']);

        $data = $this->limiter->truncate([
            'id' => $company->id,
            'name' => $company->name,
            'status' => $company->status,
            'email' => $company->email,
            'phone' => $company->phone,
            'city' => $company->city,
            'country' => $company->country,
            'created_at' => $company->created_at?->toDateString(),
            'deals' => $canDeals
                ? Deal::query()
                    ->visibleTo($user)
                    ->where('company_id', $company->id)
                    ->latest('id')
                    ->limit($limit)
                    ->get(['id', 'title', 'status', 'value', 'currency'])
                    ->map(fn (Deal $deal) => [
                        'id' => $deal->id,
                        'title' => $deal->title,
                        'status' => $deal->status,
                        'value' => $deal->value,
                        'currency' => $deal->currency,
                    ])->all()
                : null,
            'projects' => $canProjects
                ? Project::query()
                    ->where('company_id', $company->id)
                    ->with('status:id,name')
                    ->latest('id')
                    ->limit($limit)
                    ->get(['id', 'title', 'project_status_id', 'due_date', 'payment_status'])
                    ->map(fn (Project $project) => [
                        'id' => $project->id,
                        'title' => $project->title,
                        'status' => $project->status?->name,
                        'due_date' => $project->due_date?->toDateString(),
                        'payment_status' => $project->payment_status,
                    ])->all()
                : null,
            'invoices' => $canInvoices
                ? Invoice::query()
                    ->where('company_id', $company->id)
                    ->latest('id')
                    ->limit($limit)
                    ->get(['id', 'invoice_number', 'status', 'total', 'currency', 'due_at'])
                    ->map(fn (Invoice $invoice) => [
                        'id' => $invoice->id,
                        'number' => $invoice->invoice_number,
                        'status' => $invoice->status,
                        'total' => $invoice->total,
                        'currency' => $invoice->currency,
                        'due_at' => $invoice->due_at?->toDateString(),
                    ])->all()
                : null,
        ]);

        $sources = ['Customers'];
        if ($canDeals) {
            $sources[] = 'Deals';
        }
        if ($canProjects) {
            $sources[] = 'Projects';
        }
        if ($canInvoices) {
            $sources[] = 'Invoices';
        }

        return ToolResult::success($data, $sources);
    }
}
