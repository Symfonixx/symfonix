<?php

namespace Modules\CRM\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Base\Support\Meta;
use Modules\CRM\Models\Subscription;
use Modules\Finance\Models\Invoice;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Subscription::class);

        /** @var User $user */
        $user = $request->user();

        $subscriptions = Subscription::query()
            ->whereIn('company_id', $user->companyIds())
            ->with(['company:id,name', 'service:id,title', 'services:id,title'])
            ->latest('renewal_at')
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Subscription $subscription) => $this->formatSubscriptionSummary($subscription));

        return $this->inertia('User::Portal/Subscriptions/Index', [
            'subscriptions' => $subscriptions,
        ], (new Meta)
            ->title(__('user::portal.pages.subscriptions_title'))
            ->description(__('user::portal.pages.subscriptions_description'))
            ->robots('noindex, nofollow')
            ->toArray());
    }

    public function show(Request $request, Subscription $subscription)
    {
        $this->authorize('view', $subscription);

        $subscription->load([
            'company:id,name',
            'service:id,title',
            'services:id,title',
            'invoices' => fn ($q) => $q
                ->whereNotIn('status', [Invoice::STATUS_DRAFT, Invoice::STATUS_VOID]),
        ]);

        $invoices = $subscription->invoices->map(fn (Invoice $invoice) => [
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'status' => $invoice->status,
            'total' => (float) $invoice->total,
            'currency' => $invoice->currency,
            'issued_at' => $invoice->issued_at?->toDateString(),
            'due_at' => $invoice->due_at?->toDateString(),
            'paid_at' => $invoice->paid_at?->toDateString(),
            'pdf_url' => route('portal.invoices.pdf', $invoice),
        ]);

        return $this->inertia('User::Portal/Subscriptions/Show', [
            'subscription' => [
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
            ],
            'invoices' => $invoices->values()->all(),
        ], (new Meta)
            ->title($subscription->name.' | '.__('user::portal.pages.subscriptions_title'))
            ->description(__('user::portal.pages.subscriptions_description'))
            ->robots('noindex, nofollow')
            ->toArray());
    }

    private function formatSubscriptionSummary(Subscription $subscription): array
    {
        return [
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
        ];
    }
}
