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
        $subscription = $this->createSubscriptionAction->execute($data);

        if ($request->boolean('redirect_to_company') && $subscription) {
            return redirect()->route('admin.companies.show', $subscription->company_id);
        }

        return redirect()->route('admin.subscriptions.index');
    }

    public function show(Subscription $subscription)
    {
        $subscription->loadMissing([
            'company:id,name,email,phone,status',
            'crmActivities.user:id,name',
            'crmAuditLogs.user:id,name',
        ]);

        return view('crm::admin.subscription.show', compact('subscription'));
    }

    public function edit(Subscription $subscription)
    {
        return view('crm::admin.subscription.edit', array_merge(
            ['subscription' => $subscription],
            $this->formData(request())
        ));
    }

    public function update(UpdateSubscriptionRequest $request, Subscription $subscription): RedirectResponse
    {
        $data = SubscriptionData::fromRequest($request->validated());
        $this->updateSubscriptionAction->execute($subscription, $data);

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
            'selectedCompanyId' => $request->integer('company_id') ?: null,
        ];
    }
}
