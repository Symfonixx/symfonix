<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\AI\Support\CostLimiter;
use Modules\AI\Support\ToolResult;
use Modules\CRM\Models\Deal;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\InvoiceLine;
use Modules\Finance\Services\CurrencyService;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductSale;
use Modules\Services\Models\Service;

class GetBestSellingServicesTool extends AbstractAssistantTool
{
    public function __construct(
        CostLimiter $limiter,
        private readonly CurrencyService $currencyService,
    ) {
        parent::__construct($limiter);
    }

    public function name(): string
    {
        return 'get_best_selling_services';
    }

    public function description(): string
    {
        return 'Get best-selling services from paid invoice lines and won deals, best-selling products, and most-visited service pages. Use this when asked which services sell best.';
    }

    public function parameters(): array
    {
        return $this->periodParameters();
    }

    public function permissions(): array
    {
        return [
            'finance.invoices.view',
            'finance.dashboard.view',
            'sales.deals.view',
            'finance.product_sales.view',
            'product.catalog.view',
            'services.catalog.view',
        ];
    }

    protected function execute(User $user, array $arguments): ToolResult
    {
        $range = $this->period($arguments);
        $limit = $this->limiter->maxListItems();
        $currency = $this->currencyService->displayCurrency();
        $canFinance = $user->canany(['finance.invoices.view', 'finance.dashboard.view']);
        $canDeals = $user->can('sales.deals.view');
        $canProducts = $user->canany(['finance.product_sales.view', 'product.catalog.view']);
        $canServices = $user->can('services.catalog.view');

        $data = [
            'period' => $range['period'],
            'period_label' => $range['source_label'],
            'currency' => $currency,
            'paid_invoice_services' => $canFinance ? $this->paidInvoiceServices($range, $limit) : null,
            'won_deal_services' => $canDeals ? $this->wonDealServices($user, $range, $limit) : null,
            'product_sales' => $canProducts ? $this->productSales($range, $limit) : null,
            'most_visited_service_pages' => $canServices ? $this->mostVisitedServices($limit) : null,
            'how_to_read' => [
                'paid_invoice_services' => 'Revenue from paid invoice lines linked to services. Use this first for "best selling".',
                'won_deal_services' => 'Value of services attached to won deals in the period.',
                'product_sales' => 'Catalog product sales, not services.',
                'most_visited_service_pages' => 'Public service page views. This is popularity, not revenue.',
            ],
        ];

        if (
            $data['paid_invoice_services'] === null
            && $data['won_deal_services'] === null
            && $data['product_sales'] === null
            && $data['most_visited_service_pages'] === null
        ) {
            return ToolResult::denied($this->deniedMessage());
        }

        $sources = [$range['source_label']];
        if ($canFinance) {
            $sources[] = 'Invoices';
            $sources[] = 'Services';
        }
        if ($canDeals) {
            $sources[] = 'Deals';
        }
        if ($canProducts) {
            $sources[] = 'Product sales';
        }
        if ($canServices) {
            $sources[] = 'Service page visits';
        }

        return ToolResult::success($data, array_values(array_unique($sources)));
    }

    /**
     * @param  array{start: Carbon, end: Carbon}  $range
     * @return list<array<string, mixed>>
     */
    private function paidInvoiceServices(array $range, int $limit): array
    {
        $rows = InvoiceLine::query()
            ->whereNotNull('service_id')
            ->whereHas('invoice', function ($query) use ($range) {
                $query->where('status', Invoice::STATUS_PAID)
                    ->whereBetween('paid_at', [$range['start'], $range['end']]);
            })
            ->selectRaw('service_id, SUM(amount) as revenue, SUM(quantity) as quantity, COUNT(*) as line_count')
            ->groupBy('service_id')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();

        $services = Service::query()
            ->whereIn('id', $rows->pluck('service_id')->filter())
            ->get(['id', 'title'])
            ->keyBy('id');

        return $rows->map(fn (InvoiceLine $row) => [
            'service_id' => $row->service_id,
            'name' => $this->serviceName($services->get($row->service_id)),
            'revenue' => round((float) $row->revenue, 2),
            'quantity' => round((float) $row->quantity, 2),
            'invoice_lines' => (int) $row->line_count,
        ])->all();
    }

    /**
     * @param  array{start: Carbon, end: Carbon}  $range
     * @return list<array<string, mixed>>
     */
    private function wonDealServices(User $user, array $range, int $limit): array
    {
        $dealIds = Deal::query()
            ->visibleTo($user)
            ->where('status', Deal::STATUS_WON)
            ->whereBetween('won_at', [$range['start'], $range['end']])
            ->pluck('id');

        if ($dealIds->isEmpty()) {
            return [];
        }

        $rows = DB::table('deal_service')
            ->whereIn('deal_id', $dealIds)
            ->selectRaw('service_id, SUM(quantity * unit_price) as revenue, SUM(quantity) as quantity, COUNT(*) as deals')
            ->groupBy('service_id')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();

        $services = Service::query()
            ->whereIn('id', $rows->pluck('service_id')->filter())
            ->get(['id', 'title'])
            ->keyBy('id');

        return $rows->map(fn ($row) => [
            'service_id' => $row->service_id,
            'name' => $this->serviceName($services->get($row->service_id)),
            'revenue' => round((float) $row->revenue, 2),
            'quantity' => round((float) $row->quantity, 2),
            'won_deals' => (int) $row->deals,
        ])->all();
    }

    /**
     * @param  array{start: Carbon, end: Carbon}  $range
     * @return list<array<string, mixed>>
     */
    private function productSales(array $range, int $limit): array
    {
        $rows = ProductSale::query()
            ->whereBetween('sold_at', [$range['start'], $range['end']])
            ->selectRaw('product_id, SUM(total_amount) as revenue, SUM(quantity) as quantity, COUNT(*) as sales')
            ->groupBy('product_id')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();

        $products = Product::query()
            ->whereIn('id', $rows->pluck('product_id')->filter())
            ->get(['id', 'name'])
            ->keyBy('id');

        return $rows->map(function (ProductSale $row) use ($products) {
            $name = $products->get($row->product_id)?->name;
            if (is_array($name)) {
                $name = $name[app()->getLocale()] ?? reset($name) ?: null;
            }

            return [
                'product_id' => $row->product_id,
                'name' => is_string($name) && $name !== '' ? $name : __('ai::assistant.uncategorized'),
                'revenue' => round((float) $row->revenue, 2),
                'quantity' => (int) $row->quantity,
                'sales' => (int) $row->sales,
            ];
        })->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function mostVisitedServices(int $limit): array
    {
        return Service::query()
            ->orderByDesc('visits')
            ->limit($limit)
            ->get(['id', 'title', 'visits'])
            ->map(fn (Service $service) => [
                'service_id' => $service->id,
                'name' => $this->serviceName($service),
                'page_visits' => (int) $service->visits,
            ])
            ->all();
    }

    private function serviceName(?Service $service): string
    {
        if ($service === null) {
            return __('ai::assistant.uncategorized');
        }

        $title = $service->title;
        if (is_array($title)) {
            $title = $title[app()->getLocale()] ?? reset($title) ?: null;
        }

        return is_string($title) && $title !== '' ? $title : __('ai::assistant.uncategorized');
    }
}
