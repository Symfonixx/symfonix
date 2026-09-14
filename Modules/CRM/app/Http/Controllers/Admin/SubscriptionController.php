<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Requests\DeleteMultiRequest;
use Modules\CRM\Actions\Subscription\BulkDeleteSubscriptionsAction;
use Modules\CRM\Actions\Subscription\CreateSubscriptionAction;
use Modules\CRM\Actions\Subscription\DeleteSubscriptionAction;
use Modules\CRM\Actions\Subscription\ListSubscriptionsAction;
use Modules\CRM\Actions\Subscription\UpdateSubscriptionAction;
use Modules\CRM\DTOs\Subscription\SubscriptionData;
use Modules\CRM\Http\Requests\StoreSubscriptionRequest;
use Modules\CRM\Http\Requests\SubscriptionIndexRequest;
use Modules\CRM\Http\Requests\UpdateSubscriptionRequest;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Subscription;
use Modules\Services\Models\Service;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly ListSubscriptionsAction $listSubscriptionsAction,
        private readonly CreateSubscriptionAction $createSubscriptionAction,
        private readonly UpdateSubscriptionAction $updateSubscriptionAction,
        private readonly DeleteSubscriptionAction $deleteSubscriptionAction,
        private readonly BulkDeleteSubscriptionsAction $bulkDeleteSubscriptionsAction,
    ) {
        $this->authorizeResource(Subscription::class, 'subscription');
        $this->setActive('crm');
        $this->setActive('subscriptions');
    }

    public function index(SubscriptionIndexRequest $request)
    {
        $filters = $request->validated();
        $model = $this->listSubscriptionsAction->execute($filters);
        $companies = Company::query()->select(['id', 'name'])->orderBy('name')->get();

        return view('crm::admin.subscription.index', compact('model', 'filters', 'companies'));
    }

    public function create(Request $request)
    {
        return view('crm::admin.subscription.create', $this->formData($request));
    }

    public function store(StoreSubscriptionRequest $request): RedirectResponse
    {
        $data = SubscriptionData::fromRequest($request->validated());
        $subscription = $this->createSubscriptionAction->execute($data, $request->input('service_ids', []));

        if ($request->boolean('redirect_to_company') && $subscription) {
            return redirect()->route('admin.companies.show', $subscription->company_id);
        }

        return redirect()->route('admin.subscriptions.index');
    }

    public function show(Subscription $subscription)
    {
        $subscription->loadMissing([
            'company:id,name,email,phone,status',
            'service:id,title',
            'services:id,title',
            'invoices',
            'crmActivities' => fn ($q) => $q->with('user:id,name')->latest()->limit(50),
            'crmAuditLogs' => fn ($q) => $q->with('user:id,name')->latest('created_at')->limit(50),
        ]);

        return view('crm::admin.subscription.show', compact('subscription'));
    }

    public function edit(Subscription $subscription)
    {
        $subscription->loadMissing('services:id,title');

        return view('crm::admin.subscription.edit', array_merge(
            ['subscription' => $subscription],
            $this->formData(request())
        ));
    }

    public function update(UpdateSubscriptionRequest $request, Subscription $subscription): RedirectResponse
    {
        $data = SubscriptionData::fromRequest($request->validated());
        $this->updateSubscriptionAction->execute($subscription, $data, $request->input('service_ids', []));

        return redirect()->route('admin.subscriptions.index');
    }

    public function destroy(Subscription $subscription): RedirectResponse
    {
        $this->deleteSubscriptionAction->execute($subscription);

        return redirect()->route('admin.subscriptions.index');
    }

    public function deleteMulti(DeleteMultiRequest $request): RedirectResponse
    {
        $this->bulkDeleteSubscriptionsAction->execute($request->input('ids', []));

        return back();
    }

    private function formData(Request $request): array
    {
        return [
            'companies' => Company::query()->select(['id', 'name'])->orderBy('name')->get(),
            'services' => Service::query()->published()->select(['id', 'title'])->orderBy('title')->get(),
            'selectedCompanyId' => $request->integer('company_id') ?: null,
        ];
    }
}
