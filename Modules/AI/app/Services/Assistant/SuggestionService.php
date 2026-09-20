<?php

namespace Modules\AI\Services\Assistant;

use App\Models\User;

class SuggestionService
{
    /**
     * @return list<array{id: string, icon: string, prompt: string, label: string}>
     */
    public function forUser(User $user): array
    {
        $suggestions = [
            [
                'id' => 'invoices',
                'icon' => 'bi-receipt',
                'prompt' => __('ai::assistant.suggestions.invoices.prompt'),
                'label' => __('ai::assistant.suggestions.invoices.label'),
                'permissions' => ['finance.invoices.view', 'finance.dashboard.view'],
            ],
            [
                'id' => 'overdue_tasks',
                'icon' => 'bi-exclamation-circle',
                'prompt' => __('ai::assistant.suggestions.overdue_tasks.prompt'),
                'label' => __('ai::assistant.suggestions.overdue_tasks.label'),
                'permissions' => ['crm.activities.view'],
            ],
            [
                'id' => 'overdue_projects',
                'icon' => 'bi-kanban',
                'prompt' => __('ai::assistant.suggestions.overdue_projects.prompt'),
                'label' => __('ai::assistant.suggestions.overdue_projects.label'),
                'permissions' => ['project.projects.view'],
            ],
            [
                'id' => 'revenue',
                'icon' => 'bi-cash-stack',
                'prompt' => __('ai::assistant.suggestions.revenue.prompt'),
                'label' => __('ai::assistant.suggestions.revenue.label'),
                'permissions' => ['finance.dashboard.view'],
            ],
            [
                'id' => 'growth',
                'icon' => 'bi-graph-up-arrow',
                'prompt' => __('ai::assistant.suggestions.growth.prompt'),
                'label' => __('ai::assistant.suggestions.growth.label'),
                'permissions' => ['finance.dashboard.view'],
            ],
            [
                'id' => 'customers',
                'icon' => 'bi-people',
                'prompt' => __('ai::assistant.suggestions.customers.prompt'),
                'label' => __('ai::assistant.suggestions.customers.label'),
                'permissions' => ['finance.invoices.view', 'finance.dashboard.view'],
            ],
            [
                'id' => 'leads',
                'icon' => 'bi-person-plus',
                'prompt' => __('ai::assistant.suggestions.leads.prompt'),
                'label' => __('ai::assistant.suggestions.leads.label'),
                'permissions' => ['crm.leads.view'],
            ],
            [
                'id' => 'today',
                'icon' => 'bi-sun',
                'prompt' => __('ai::assistant.suggestions.today.prompt'),
                'label' => __('ai::assistant.suggestions.today.label'),
                'permissions' => [],
            ],
            [
                'id' => 'visitors',
                'icon' => 'bi-bar-chart-line',
                'prompt' => __('ai::assistant.suggestions.visitors.prompt'),
                'label' => __('ai::assistant.suggestions.visitors.label'),
                'permissions' => ['support.visitors.view', 'overview.dashboard.view'],
            ],
            [
                'id' => 'services',
                'icon' => 'bi-bag-check',
                'prompt' => __('ai::assistant.suggestions.services.prompt'),
                'label' => __('ai::assistant.suggestions.services.label'),
                'permissions' => [
                    'finance.invoices.view',
                    'finance.dashboard.view',
                    'sales.deals.view',
                    'services.catalog.view',
                    'finance.product_sales.view',
                    'product.catalog.view',
                ],
            ],
            [
                'id' => 'employees',
                'icon' => 'bi-person-badge',
                'prompt' => __('ai::assistant.suggestions.employees.prompt'),
                'label' => __('ai::assistant.suggestions.employees.label'),
                'permissions' => ['reporting.employee.view', 'hr.employees.view'],
            ],
        ];

        $out = [];

        foreach ($suggestions as $suggestion) {
            $permissions = $suggestion['permissions'];
            if ($permissions !== [] && ! $user->canany($permissions)) {
                continue;
            }

            $out[] = [
                'id' => $suggestion['id'],
                'icon' => $suggestion['icon'],
                'prompt' => $suggestion['prompt'],
                'label' => $suggestion['label'],
            ];
        }

        return $out;
    }
}
