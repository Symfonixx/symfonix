<?php

namespace Modules\Services\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Requests\DeleteMultiRequest;
use Modules\CRM\Services\Marketing\ContentMarketingEmailSender;
use Modules\Services\Enums\ServiceStatus;
use Modules\Services\Models\Service;
use Modules\Services\Models\ServiceCategory;
use Modules\Services\Repositories\Service\ServiceRepository;

class ServiceController extends Controller
{
    protected ServiceRepository $serviceRepository;

    public function __construct(
        ServiceRepository $serviceRepository,
        private readonly ContentMarketingEmailSender $contentMarketingEmailSender,
    ) {
        $this->serviceRepository = $serviceRepository;
        $this->authorizeResource(Service::class, 'service');
        $this->setActive('services');
    }

    public function index()
    {
        $model = $this->serviceRepository->all([
            'id', 'title', 'slug', 'image', 'status', 'featured', 'visits', 'created_at', 'service_category_id',
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
        $this->contentMarketingEmailSender->validate($request);

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
            'auto_translate' => $request->boolean('auto_translate'),
        ];
        $this->serviceRepository->store($data);

        if ($this->contentMarketingEmailSender->shouldSend($request)) {
            try {
                $campaign = $this->contentMarketingEmailSender->send(
                    $request,
                    (string) $request->input('title'),
                    $this->contentMarketingEmailSender->buildBody(
                        $request->input('description'),
                        $request->input('content'),
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
            'auto_translate' => $request->boolean('auto_translate'),
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
