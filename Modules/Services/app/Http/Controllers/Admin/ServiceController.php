<?php

namespace Modules\Services\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Requests\DeleteMultiRequest;
use Modules\Services\Enums\ServiceStatus;
use Modules\Services\Models\Service;
use Modules\Services\Models\ServiceCategory;
use Modules\Services\Repositories\Service\ServiceRepository;

class ServiceController extends Controller
{
    protected ServiceRepository $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
        $this->setActive('services');
    }

    public function index()
    {
        $model = $this->serviceRepository->all([
            'id', 'title', 'slug', 'image', 'status', 'featured', 'visits', 'created_at', 'service_category_id'
        ]);

        return view('services::admin.service.index', compact('model'));
    }

    public function create()
    {
        $categories = ServiceCategory::select('id', 'title')->latest()->get();

        return view('services::admin.service.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = [
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'description' => $request->input('description'),
            'content' => $request->input('content'),
            'keywords' => $request->input('keywords'),
            'image' => $request->file('img'),
            'service_category_id' => $request->input('service_category_id'),
            'status' => $request->input('status') ? ServiceStatus::PUBLISHED : ServiceStatus::ARCHIVED,
            'featured' => $request->boolean('featured'),
        ];
        $this->serviceRepository->store($data);

        return redirect()->route('admin.services.index');
    }

    public function edit(Service $service)
    {
        $categories = ServiceCategory::select('id', 'title')->latest()->get();
        return view('services::admin.service.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $data = [
            'title' => $request->input('title'),
            'slug' => $service->slug,
            'description' => $request->input('description'),
            'content' => $request->input('content'),
            'keywords' => $request->input('keywords'),
            'image' => $request->file('img'),
            'service_category_id' => $request->input('service_category_id'),
            'status' => $request->input('status') ? ServiceStatus::PUBLISHED : ServiceStatus::ARCHIVED,
            'featured' => $request->boolean('featured'),
        ];
        $this->serviceRepository->update($data, $service);

        return redirect()->route('admin.services.index');
    }

    public function deleteMulti(DeleteMultiRequest $request): RedirectResponse
    {
        $this->serviceRepository->deleteMulti($request->input('ids'));

        return back();
    }
}
