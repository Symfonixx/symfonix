<?php

namespace Modules\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\CRM\Services\Marketing\ContentMarketingEmailSender;
use Modules\Product\Http\Requests\ProductIndexRequest;
use Modules\Product\Http\Requests\StoreProductRequest;
use Modules\Product\Http\Requests\UpdateProductRequest;
use Modules\Product\Models\Product;
use Modules\Product\Repositories\ProductCategoryRepository;
use Modules\Product\Repositories\ProductRepository;
use Modules\Tax\Services\TaxRate\TaxRateService;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly ProductCategoryRepository $categoryRepository,
        private readonly ContentMarketingEmailSender $contentMarketingEmailSender,
    ) {
        $this->authorizeResource(Product::class, 'product');
        $this->setActive('products');
    }

    public function index(ProductIndexRequest $request)
    {
        $filters = $request->validated();

        if (! $request->has('status')) {
            $filters['status'] = Product::STATUS_ACTIVE;
        }

        $model = $this->productRepository->paginate($filters, (int) config('core.page_size', 15));
        $categories = $this->categoryRepository->allOrdered();

        return view('product::admin.product.index', compact('model', 'filters', 'categories'));
    }

    public function create()
    {
        return view('product::admin.product.create', $this->formData());
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->productRepository->store($request->validated());

        if ($this->contentMarketingEmailSender->shouldSend($request)) {
            try {
                $campaign = $this->contentMarketingEmailSender->send(
                    $request,
                    (string) $request->input('name'),
                    $this->contentMarketingEmailSender->buildBody(
                        $request->input('short_description'),
                        $request->input('description'),
                    ),
                );

                session()->flushMessage(
                    true,
                    __('crm::marketing.messages.queued', ['count' => $campaign->recipients_count]),
                );
            } catch (\Throwable $e) {
                report($e);
                session()->flushMessage(false, __('crm::marketing.messages.send_failed'));
            }
        }

        return redirect()->route('admin.products.index');
    }

    public function edit(Product $product)
    {
        return view('product::admin.product.edit', array_merge(['product' => $product], $this->formData()));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->productRepository->update($product, $request->validated());

        return redirect()->route('admin.products.index');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->productRepository->delete($product);

        return redirect()->route('admin.products.index');
    }

    public function togglePublished(Product $product): RedirectResponse
    {
        $this->authorize('update', $product);
        $this->productRepository->togglePublished($product);

        return back();
    }

    private function formData(): array
    {
        return [
            'categories' => $this->categoryRepository->allOrdered(),
            'taxRates' => app(TaxRateService::class)->activeOptions(),
        ];
    }
}
