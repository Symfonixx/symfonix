<?php

namespace Modules\Finance\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\CRM\Models\Company;
use Modules\Finance\Http\Requests\StoreProductSaleRequest;
use Modules\Finance\Services\FinanceService;
use Modules\Finance\Services\InvoiceService;
use Modules\Product\Models\ProductSale;
use Modules\Product\Repositories\ProductRepository;
use Modules\Tax\Services\TaxRate\TaxRateService;

class ProductSaleController extends Controller
{
    public function __construct(
        private readonly FinanceService $financeService,
        private readonly InvoiceService $invoiceService,
        private readonly ProductRepository $productRepository,
        private readonly TaxRateService $taxRateService,
    ) {
        $this->setActive('finance_product_sales');
    }

    public function index(): View
    {
        $this->authorize('viewAny', ProductSale::class);

        $sales = ProductSale::query()
            ->with(['product:id,name,sku', 'company:id,name', 'deal:id,title', 'seller:id,name', 'invoice:id,invoice_number'])
            ->latest('sold_at')
            ->paginate((int) config('core.page_size', 15));

        $products = $this->productRepository->activeProducts()->load('taxRate:id,name,percentage,type');
        $companies = Company::query()->orderBy('name')->get(['id', 'name']);
        $users = User::query()->orderBy('name')->get(['id', 'name']);
        $taxRates = $this->taxRateService->activeOptions();

        return view('finance::admin.product_sale.index', compact('sales', 'products', 'companies', 'users', 'taxRates'));
    }

    public function store(StoreProductSaleRequest $request): RedirectResponse
    {
        $this->authorize('create', ProductSale::class);

        $sale = $this->financeService->recordProductSale($request->validated());

        if ($sale && $sale->company_id) {
            $invoice = $this->invoiceService->createFromProductSale($sale);

            session()->flushMessage(
                true,
                $invoice
                    ? __('finance::product_sale.messages.recorded_with_invoice', ['number' => $invoice->invoice_number])
                    : __('finance::product_sale.messages.recorded')
            );
        } else {
            session()->flushMessage(true, __('finance::product_sale.messages.recorded'));
        }

        return back();
    }

    public function destroy(ProductSale $productSale): RedirectResponse
    {
        $this->authorize('delete', $productSale);

        $this->financeService->deleteProductSale($productSale);

        session()->flushMessage(true, __('finance::product_sale.messages.deleted'));

        return back();
    }
}
