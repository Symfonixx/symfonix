<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\Component;
use Modules\CRM\Models\ContactForm;
use Modules\CRM\Models\CrmActivity;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\Quote;
use Modules\Finance\Models\Invoice;
use Modules\Support\Models\Ticket;
use Modules\Testimonial\Models\Testimonial;

class AdminLayout extends Component
{
    protected \Illuminate\Contracts\Auth\Authenticatable|null|\App\Models\User $user;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->user = auth()->user();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $recentMessages = collect();
        $unreadMessageCount = 0;
        $recentNotifications = collect();
        $unreadNotificationCount = 0;
        $pendingTestimonialCount = 0;
        $navCounts = [
            'open_deals' => 0,
            'new_leads' => 0,
            'pending_tasks' => 0,
            'open_tickets' => 0,
            'pending_quotes' => 0,
            'pending_inquiries' => 0,
            'open_invoices' => 0,
        ];

        if ($this->user) {
            $unreadNotificationCount = $this->user->unreadNotifications()->count();
            $recentNotifications = $this->user->notifications()
                ->latest()
                ->limit(5)
                ->get();
        }

        if ($this->user?->can('CRM Management')) {
            $recentMessages = ContactForm::query()
                ->latest()
                ->limit(5)
                ->get(['id', 'name', 'subject', 'created_at']);

            $unreadMessageCount = ContactForm::query()
                ->where('created_at', '>=', now()->subDays(7))
                ->count();

            $navCounts['pending_inquiries'] = $unreadMessageCount;
            $navCounts['open_deals'] = Deal::query()->visibleTo()->where('status', Deal::STATUS_OPEN)->count();
            $navCounts['new_leads'] = Lead::query()->where('status', Lead::STATUS_NEW)->count();
            if (Schema::hasTable('crm_activities')) {
                $navCounts['pending_tasks'] = CrmActivity::query()
                    ->where('type', CrmActivity::TYPE_TASK)
                    ->whereNull('completed_at')
                    ->count();
            }
            if (Schema::hasTable('quotes')) {
                $navCounts['pending_quotes'] = Quote::query()->where('status', Quote::STATUS_SENT)->count();
            }
        }

        if ($this->user?->can('Support Management')) {
            $navCounts['open_tickets'] = Ticket::query()
                ->whereIn('status', [Ticket::STATUS_OPEN, Ticket::STATUS_IN_PROGRESS])
                ->count();
        }

        if ($this->user?->can('Finance Management') && class_exists(Invoice::class)) {
            $navCounts['open_invoices'] = Invoice::query()
                ->whereIn('status', [Invoice::STATUS_SENT, Invoice::STATUS_OVERDUE])
                ->count();
        }

        if ($this->user?->can('Testimonials Management')) {
            $pendingTestimonialCount = Testimonial::query()
                ->where('status', 'Archived')
                ->count();
        }

        return view('components.admin-layout', [
            'user' => $this->user,
            'recentMessages' => $recentMessages,
            'unreadMessageCount' => $unreadMessageCount,
            'recentNotifications' => $recentNotifications,
            'unreadNotificationCount' => $unreadNotificationCount,
            'pendingTestimonialCount' => $pendingTestimonialCount,
            'navCounts' => $navCounts,
        ]);
    }
}
