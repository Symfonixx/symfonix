<?php

namespace Modules\CRM\Services\Dashboard;

use App\Models\User;
use Illuminate\Support\Collection;
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
            ['id' => 'pipeline_funnel', 'type' => 'chart', 'span' => 'col-xl-12', 'icon' => 'funnel', 'color' => 'info'],
            ['id' => 'open_leads_by_stages', 'type' => 'chart', 'span' => 'col-xl-6', 'icon' => 'filter-circle', 'color' => 'success'],
            ['id' => 'lead_channels', 'type' => 'chart', 'span' => 'col-xl-6', 'icon' => 'diagram-3', 'color' => 'warning'],
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

        $merged = $this->insertMissingCatalogWidgets($merged, $seen, $catalog);
        $merged = $this->ensureWidgetOrder($merged, 'open_leads_by_stages', 'lead_channels');

        return $this->reindexWidgets($merged);
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

        $seen = collect($normalized)->pluck('id')->flip()->all();
        $normalized = collect($this->insertMissingCatalogWidgets(
            collect($normalized)->map(fn (array $item) => [
                ...$catalog->get($item['id']),
                'visible' => $item['visible'],
            ])->values()->all(),
            $seen,
            $catalog,
        ))->map(fn (array $widget) => [
            'id' => $widget['id'],
            'visible' => $widget['visible'],
        ])->all();

        $normalized = collect($this->ensureWidgetOrder(
            collect($normalized)->map(fn (array $item, int $index) => [
                ...$catalog->get($item['id']),
                'visible' => $item['visible'],
                'order' => $index,
            ])->values()->all(),
            'open_leads_by_stages',
            'lead_channels',
        ))->map(fn (array $widget, int $index) => [
            'id' => $widget['id'],
            'visible' => $widget['visible'],
            'order' => $index,
        ])->all();

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

    /**
     * @param  list<array{id: string, type: string, span: string, visible: bool, order: int, icon: string, color: string}>  $merged
     * @param  array<string, true>  $seen
     * @param  Collection<string, array{id: string, type: string, span: string, visible: bool, order: int, icon: string, color: string}>  $catalog
     * @return list<array{id: string, type: string, span: string, visible: bool, order: int, icon: string, color: string}>
     */
    private function insertMissingCatalogWidgets(array $merged, array $seen, $catalog): array
    {
        $catalogOrder = $catalog->values()->pluck('id')->flip();

        foreach ($catalog as $def) {
            if (isset($seen[$def['id']])) {
                continue;
            }

            $targetIndex = $catalogOrder->get($def['id'], count($merged));
            $insertAt = 0;

            foreach ($merged as $index => $widget) {
                $widgetIndex = $catalogOrder->get($widget['id'], PHP_INT_MAX);
                if ($widgetIndex < $targetIndex) {
                    $insertAt = $index + 1;
                }
            }

            array_splice($merged, $insertAt, 0, [$def]);
            $seen[$def['id']] = true;
        }

        return $merged;
    }

    /**
     * @param  list<array{id: string, type: string, span: string, visible: bool, order: int, icon: string, color: string}>  $widgets
     * @return list<array{id: string, type: string, span: string, visible: bool, order: int, icon: string, color: string}>
     */
    private function ensureWidgetOrder(array $widgets, string $beforeId, string $afterId): array
    {
        $beforeIndex = collect($widgets)->search(fn (array $widget) => $widget['id'] === $beforeId);
        $afterIndex = collect($widgets)->search(fn (array $widget) => $widget['id'] === $afterId);

        if ($beforeIndex === false || $afterIndex === false || $beforeIndex < $afterIndex) {
            return $widgets;
        }

        $before = $widgets[$beforeIndex];
        array_splice($widgets, $beforeIndex, 1);
        $afterIndex = collect($widgets)->search(fn (array $widget) => $widget['id'] === $afterId);

        if ($afterIndex === false) {
            $widgets[] = $before;

            return $widgets;
        }

        array_splice($widgets, $afterIndex, 0, [$before]);

        return $widgets;
    }

    /**
     * @param  list<array{id: string, type: string, span: string, visible: bool, order: int, icon: string, color: string}>  $widgets
     * @return list<array{id: string, type: string, span: string, visible: bool, order: int, icon: string, color: string}>
     */
    private function reindexWidgets(array $widgets): array
    {
        return collect($widgets)->values()->map(function (array $widget, int $index) {
            $widget['order'] = $index;

            return $widget;
        })->all();
    }
}
