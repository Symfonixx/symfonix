<?php

namespace Modules\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Product\Http\Requests\StoreProductCategoryRequest;
use Modules\Product\Http\Requests\UpdateProductCategoryRequest;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Repositories\ProductCategoryRepository;

class ProductCategoryController extends Controller
{
    public function __construct(
        private readonly ProductCategoryRepository $categoryRepository,
    ) {
        $this->authorizeResource(ProductCategory::class, 'product_category');
        $this->setActive('products');
        $this->setActive('product_categories');
    }

    public function index(): View
    {
        $categories = ProductCategory::query()
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return view('product::admin.product_category.index', compact('categories'));
    }

    public function create(): View
    {
        return view('product::admin.product_category.create');
    }

    public function store(StoreProductCategoryRequest $request): RedirectResponse
    {
        $this->categoryRepository->store($request->validated());

        return redirect()->route('admin.product-categories.index');
    }

    public function edit(ProductCategory $product_category): View
    {
        return view('product::admin.product_category.edit', ['category' => $product_category]);
    }

    public function update(UpdateProductCategoryRequest $request, ProductCategory $product_category): RedirectResponse
    {
        $this->categoryRepository->update($product_category, $request->validated());

        return redirect()->route('admin.product-categories.index');
    }

    public function destroy(ProductCategory $product_category): RedirectResponse
    {
        $this->categoryRepository->delete($product_category);

        return redirect()->route('admin.product-categories.index');
    }
}
