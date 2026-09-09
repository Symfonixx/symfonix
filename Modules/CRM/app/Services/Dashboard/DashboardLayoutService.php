<?php

namespace Modules\CRM\Services\Dashboard;

use App\Models\User;
use Modules\CRM\Models\CrmDashboardLayout;

class DashboardLayoutService
{
    /**
     * Full widget catalog with defaults.
     *
     * @return list<array{id: string, type: string, span: string, visible: bool, order: int, icon: string, color: string}>
     */
    public function catalog(): array
    {
        $defs = [
            ['id' => 'total_customers', 'type' => 'metric', 'span' => 'col-sm-6 col-xl-3', 'icon' => 'buildings', 'color' => 'primary'],
            ['id' => 'new_customers', 'type' => 'metric', 'span' => 'col-sm-6 col-xl-3', 'icon' => 'person-plus', 'color' => 'success'],
            ['id' => 'new_leads', 'type' => 'metric', 'span' => 'col-sm-6 col-xl-3', 'icon' => 'person-lines-fill', 'color' => 'info'],
            ['id' => 'leads_in_progress', 'type' => 'metric', 'span' => 'col-sm-6 col-xl-3', 'icon' => 'hourglass-split', 'color' => 'warning'],
            ['id' => 'active_customers', 'type' => 'metric', 'span' => 'col-sm-6 col-xl-3', 'icon' => 'check-circle', 'color' => 'success'],
            ['id' => 'lost_customers', 'type' => 'metric', 'span' => 'col-sm-6 col-xl-3', 'icon' => 'x-circle', 'color' => 'danger'],
            ['id' => 'total_sales', 'type' => 'metric', 'span' => 'col-sm-6 col-xl-3', 'icon' => 'currency-dollar', 'color' => 'primary'],
            ['id' => 'sales_this_month', 'type' => 'metric', 'span' => 'col-sm-6 col-xl-3', 'icon' => 'calendar-check', 'color' => 'success'],
            ['id' => 'pipeline_value', 'type' => 'metric', 'span' => 'col-sm-6 col-xl-3', 'icon' => 'cash-stack', 'color' => 'warning'],
            ['id' => 'won_deals', 'type' => 'metric', 'span' => 'col-sm-6 col-xl-3', 'icon' => 'trophy', 'color' => 'info'],
            ['id' => 'lost_deals', 'type' => 'metric', 'span' => 'col-sm-6 col-xl-3', 'icon' => 'emoji-frown', 'color' => 'danger'],
            ['id' => 'avg_deal_value', 'type' => 'metric', 'span' => 'col-sm-6 col-xl-3', 'icon' => 'bar-chart', 'color' => 'primary'],
            ['id' => 'conversion_rate', 'type' => 'metric', 'span' => 'col-sm-6 col-xl-3', 'icon' => 'graph-up', 'color' => 'success'],
            ['id' => 'avg_close_time', 'type' => 'metric', 'span' => 'col-sm-6 col-xl-3', 'icon' => 'stopwatch', 'color' => 'info'],
            ['id' => 'outstanding_invoices', 'type' => 'metric', 'span' => 'col-sm-6 col-xl-3', 'icon' => 'receipt', 'color' => 'warning'],
            ['id' => 'overdue_amounts', 'type' => 'metric', 'span' => 'col-sm-6 col-xl-3', 'icon' => 'exclamation-triangle', 'color' => 'danger'],
            ['id' => 'top_customers', 'type' => 'table', 'span' => 'col-xl-6', 'icon' => 'star', 'color' => 'primary'],
            ['id' => 'sales_performance', 'type' => 'table', 'span' => 'col-xl-6', 'icon' => 'people', 'color' => 'success'],
            ['id' => 'pipeline_funnel', 'type' => 'chart', 'span' => 'col-xl-8', 'icon' => 'funnel', 'color' => 'info'],
            ['id' => 'lead_channels', 'type' => 'chart', 'span' => 'col-xl-4', 'icon' => 'diagram-3', 'color' => 'warning'],
            ['id' => 'recent_activity', 'type' => 'list', 'span' => 'col-xl-12', 'icon' => 'activity', 'color' => 'secondary'],
        ];

        return collect($defs)->values()->map(function (array $def, int $index) {
            $def['visible'] = true;
            $def['order'] = $index;

            return $def;
        })->all();
    }

    /**
     * @return list<array{id: string, type: string, span: string, visible: bool, order: int, icon: string, color: string}>
     */
    public function defaults(): array
    {
        return $this->catalog();
    }

    /**
     * @return list<array{id: string, type: string, span: string, visible: bool, order: int, icon: string, color: string}>
     */
    public function forUser(User $user): array
    {
        $catalog = collect($this->catalog())->keyBy('id');
        $saved = CrmDashboardLayout::query()->where('user_id', $user->id)->value('widgets');

        if (! is_array($saved) || $saved === []) {
            return $this->defaults();
        }

        $merged = [];
        $seen = [];

        usort($saved, fn ($a, $b) => ((int) ($a['order'] ?? 0)) <=> ((int) ($b['order'] ?? 0)));

        foreach ($saved as $item) {
            $id = $item['id'] ?? null;
            if (! $id || ! $catalog->has($id) || isset($seen[$id])) {
                continue;
            }

            $def = $catalog->get($id);
            $def['visible'] = (bool) ($item['visible'] ?? true);
            $def['order'] = count($merged);
            $merged[] = $def;
            $seen[$id] = true;
        }

        foreach ($catalog as $id => $def) {
            if (isset($seen[$id])) {
                continue;
            }
            $def['order'] = count($merged);
            $merged[] = $def;
        }

        return $merged;
    }

    /**
     * @param  list<array{id: string, visible?: bool, order?: int}>  $widgets
     * @return list<array{id: string, type: string, span: string, visible: bool, order: int, icon: string, color: string}>
     */
    public function save(User $user, array $widgets): array
    {
        $catalog = collect($this->catalog())->keyBy('id');
        $normalized = [];

        usort($widgets, fn ($a, $b) => ((int) ($a['order'] ?? 0)) <=> ((int) ($b['order'] ?? 0)));

        foreach ($widgets as $item) {
            $id = $item['id'] ?? null;
            if (! $id || ! $catalog->has($id)) {
                continue;
            }

            $normalized[] = [
                'id' => $id,
                'visible' => (bool) ($item['visible'] ?? true),
                'order' => count($normalized),
            ];
        }

        foreach ($catalog->keys() as $id) {
            if (collect($normalized)->contains(fn ($w) => $w['id'] === $id)) {
                continue;
            }
            $normalized[] = [
                'id' => $id,
                'visible' => true,
                'order' => count($normalized),
            ];
        }

        CrmDashboardLayout::query()->updateOrCreate(
            ['user_id' => $user->id],
            ['widgets' => $normalized],
        );

        return $this->forUser($user->fresh());
    }

    public function reset(User $user): array
    {
        CrmDashboardLayout::query()->where('user_id', $user->id)->delete();

        return $this->defaults();
    }

    /**
     * @return list<string>
     */
    public function knownIds(): array
    {
        return collect($this->catalog())->pluck('id')->all();
    }
}
