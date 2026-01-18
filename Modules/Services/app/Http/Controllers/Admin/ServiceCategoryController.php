<?php

namespace Modules\Services\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Requests\DeleteMultiRequest;
use Modules\Services\Models\ServiceCategory;
use Modules\Services\Repositories\ServiceCategory\ServiceCategoryRepository;

class ServiceCategoryController extends Controller
{
    protected ServiceCategoryRepository $categoryRepository;

    public function __construct(ServiceCategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->setActive('services');
        $this->setActive('service_categories');
    }

    public function index()
    {
        $model = $this->categoryRepository->all(['id', 'title', 'slug', 'image' , 'color_code', 'created_at']);

        return view('services::admin.service_category.index', compact('model'));
    }

    public function create()
    {
        return view('services::admin.service_category.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = [
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'description' => $request->input('description'),
            'color_code' => $request->input('color_code'),
            'image' => $request->file('image'),
        ];
        $this->categoryRepository->store($data);

        return redirect()->route('admin.service_categories.index');
    }

    public function edit(ServiceCategory $service_category)
    {
        return view('services::admin.service_category.edit', compact('service_category'));
    }

    public function update(Request $request, ServiceCategory $service_category): RedirectResponse
    {
        $data = [
            'title' => $request->input('title'),
            'slug' => $service_category->slug,
            'description' => $request->input('description'),
            'color_code' => $request->input('color_code'),
            'image' => $request->file('image'),
        ];
        $this->categoryRepository->update($data, $service_category);

        return redirect()->route('admin.service_categories.index');
    }

    public function deleteMulti(DeleteMultiRequest $request): RedirectResponse
    {
        $this->categoryRepository->deleteMulti($request->input('ids'));

        return back();
    }
}




