<?php

namespace Modules\CRM\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Base\Support\Meta;
use Modules\CRM\Models\Subscription;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $subscriptions = Subscription::query()
            ->whereIn('company_id', $user->companyIds())
            ->with(['company:id,name', 'service:id,title', 'services:id,title'])
            ->latest('renewal_at')
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Subscription $subscription) => [
                'id' => $subscription->id,
                'name' => $subscription->name,
                'company' => $subscription->company?->only(['id', 'name']),
                'amount' => (float) $subscription->amount,
                'currency' => $subscription->currency,
                'status' => $subscription->status,
                'status_label' => __('crm::subscription.status.'.$subscription->status),
                'billing_cycle' => $subscription->billing_cycle,
                'billing_cycle_label' => __('crm::subscription.billing_cycle.'.$subscription->billing_cycle),
                'starts_at' => $subscription->starts_at?->toDateString(),
                'ends_at' => $subscription->ends_at?->toDateString(),
                'renewal_at' => $subscription->renewal_at?->toDateString(),
                'auto_renew' => (bool) $subscription->auto_renew,
                'service_name' => $subscription->service?->title
                    ?? $subscription->services->pluck('title')->filter()->implode(', '),
                'notes' => $subscription->notes,
            ]);

        return $this->inertia('User::Portal/Subscriptions/Index', [
            'subscriptions' => $subscriptions,
        ], (new Meta)
            ->title(__('user::portal.pages.subscriptions_title'))
            ->description(__('user::portal.pages.subscriptions_description'))
            ->robots('noindex, nofollow')
            ->toArray());
    }
}
