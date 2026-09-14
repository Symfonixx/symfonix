<?php

namespace Modules\User\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Modules\Cms\Enums\CmsStatus;
use Modules\Cms\Models\Blog;
use Modules\Cms\Models\Page;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\ContactForm;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\Lead;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Services\FinanceService;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductSale;
use Modules\Project\Models\Project;
use Modules\Services\Models\Service;
use Modules\Support\Enums\TicketStatus;
use Modules\Support\Models\Subscriber;
use Modules\Support\Models\Ticket;
use Modules\Testimonial\Models\Testimonial;
use Modules\User\app\Repositories\Employee\EmployeeRepository;
use Monishroy\VisitorTracking\Helpers\Visitor;
use MonishRoy\VisitorTracking\Models\VisitorTable;

class DashboardController extends Controller
{
    public function index(EmployeeRepository $employeeRepository, FinanceService $financeService): View
    {
        $this->setActive('dashboard');

        $user = auth()->user();
        $crm = $this->crmSnapshot($user);
        $tickets = $this->ticketSnapshot($user);
        $finance = $this->financeSnapshot($user, $financeService);
        $projects = $this->projectSnapshot($user);
        $products = $this->productSnapshot($user);

        return view('user::admin.dashboard.index', [
            'stats' => $this->contentStats($employeeRepository),
            'visitorsStats' => $this->visitorStats(),
            'topVisitedPages' => Visitor::topVisitedPages(10),
            'topReferrers' => Visitor::topReferrers(10),
            'visitsByMonth' => Visitor::visitsByMonth(12),
            'crmStats' => $crm['stats'],
            'ticketStats' => $tickets['stats'],
            'recentTickets' => $tickets['recent'],
            'financeStats' => $finance['stats'],
            'recentInvoices' => $finance['recent'],
            'projectStats' => $projects['stats'],
            'recentProjects' => $projects['recent'],
            'productStats' => $products['stats'],
            'recentProductSales' => $products['recent'],
            'recentContacts' => ContactForm::query()->latest()->limit(5)->get(),
            'leadStats' => $crm['lead_stats'],
            'recentLeads' => $crm['recent_leads'],
            'notificationStats' => [
                'unread' => $user?->unreadNotifications()->count() ?? 0,
            ],
            'recentNotifications' => $user
                ? $user->notifications()->latest()->limit(5)->get()
                : collect(),
            'testimonialStats' => $this->testimonialSnapshot($user),
        ]);
    }

    /**
     * @return array{totalVisitorsCount: int, todayVisitorsCount: int, weekVisitorsCount: int}
     */
    private function visitorStats(): array
    {
        return [
            'totalVisitorsCount' => VisitorTable::distinct('ip')->count('ip'),
            'todayVisitorsCount' => VisitorTable::whereDate('created_at', today())->distinct('ip')->count('ip'),
            'weekVisitorsCount' => VisitorTable::where('created_at', '>=', now()->startOfWeek())->distinct('ip')->count('ip'),
        ];
    }

    /**
     * @return array<string, int>
     */
    private function contentStats(EmployeeRepository $employeeRepository): array
    {
        return [
            'blogs' => Blog::count(),
            'leads' => Lead::count(),
            'customers' => User::customers()->count(),
            'employees' => $employeeRepository->count(),
            'subscribers' => Subscriber::count(),
            'contacts' => ContactForm::count(),
            'contacts_this_month' => ContactForm::query()
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
            'services' => Service::count(),
            'pages' => Page::count(),
        ];
    }

    /**
     * @return array{stats: ?array<string, mixed>, lead_stats: ?array<string, int>, recent_leads: Collection<int, Lead>}
     */
    private function crmSnapshot(?Authenticatable $user): array
    {
        if (! $user?->canAny(['crm.leads.view', 'crm.companies.view', 'sales.deals.view'])) {
            return ['stats' => null, 'lead_stats' => null, 'recent_leads' => collect()];
        }

        $openDeals = Deal::query()
            ->visibleTo()
            ->where('status', Deal::STATUS_OPEN)
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(value), 0) as pipeline')
            ->first();

        return [
            'stats' => [
                'companies' => Company::query()->count(),
                'deals' => Deal::query()->visibleTo()->count(),
                'open_deals' => (int) ($openDeals->count ?? 0),
                'pipeline_value' => (float) ($openDeals->pipeline ?? 0),
                'won_this_month' => Deal::query()->visibleTo()
                    ->where('status', Deal::STATUS_WON)
                    ->whereBetween('won_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->count(),
                'currency' => config('crm.default_currency', 'USD'),
            ],
            'lead_stats' => [
                'new' => Lead::query()->where('status', Lead::STATUS_NEW)->count(),
                'this_month' => Lead::query()
                    ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->count(),
            ],
            'recent_leads' => Lead::query()
                ->with('assignee:id,name')
                ->latest()
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * @return array{stats: ?array<string, int>, recent: Collection<int, Ticket>}
     */
    private function ticketSnapshot(?Authenticatable $user): array
    {
        if (! $user?->can('support.tickets.view')) {
            return ['stats' => null, 'recent' => collect()];
        }

        $counts = Ticket::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'stats' => [
                'total' => (int) $counts->sum(),
                'open' => (int) $counts->only(TicketStatus::openValues())->sum(),
                'resolved' => (int) ($counts[Ticket::STATUS_RESOLVED] ?? 0),
                'closed' => (int) ($counts[Ticket::STATUS_CLOSED] ?? 0),
            ],
            'recent' => Ticket::query()
                ->with(['user:id,name,email', 'category:id,name'])
                ->latest()
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * @return array{stats: ?array<string, mixed>, recent: Collection<int, Invoice>}
     */
    private function financeSnapshot(?Authenticatable $user, FinanceService $financeService): array
    {
        if (! $user?->can('finance.dashboard.view')) {
            return ['stats' => null, 'recent' => collect()];
        }

        $monthSummary = $financeService->getMetricsSummary([now()->format('Y-m')]);
        $receivable = $financeService->getAccountsReceivableAging();

        return [
            'stats' => [
                'revenue_this_month' => $monthSummary['revenue'],
                'outstanding' => $receivable['total_outstanding'],
                'open_invoices' => Invoice::query()->open()->count(),
                'paid_this_month' => (float) Invoice::query()
                    ->where('status', Invoice::STATUS_PAID)
                    ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->sum('total'),
                'currency' => $receivable['currency'],
            ],
            'recent' => Invoice::query()
                ->with('company:id,name')
                ->latest()
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * @return array{stats: ?array<string, int>, recent: Collection<int, Project>}
     */
    private function projectSnapshot(?Authenticatable $user): array
    {
        if (! $user?->can('project.projects.view')) {
            return ['stats' => null, 'recent' => collect()];
        }

        return [
            'stats' => [
                'total' => Project::query()->count(),
                'active' => Project::query()
                    ->whereHas('status', fn ($query) => $query->where('name', '!=', 'Completed'))
                    ->count(),
                'unpaid' => Project::query()
                    ->whereIn('payment_status', [Project::PAYMENT_UNPAID, Project::PAYMENT_PARTIALLY_PAID])
                    ->count(),
            ],
            'recent' => Project::query()
                ->with(['company:id,name', 'status:id,name,color_code'])
                ->latest()
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * @return array{stats: ?array<string, mixed>, recent: Collection<int, ProductSale>}
     */
    private function productSnapshot(?Authenticatable $user): array
    {
        if (! $user?->can('product.catalog.view')) {
            return ['stats' => null, 'recent' => collect()];
        }

        $salesThisMonth = ProductSale::query()
            ->whereBetween('sold_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total_amount), 0) as revenue')
            ->first();

        return [
            'stats' => [
                'total' => Product::query()->count(),
                'published' => Product::query()->where('is_published', true)->count(),
                'sales_this_month' => (int) ($salesThisMonth->count ?? 0),
                'revenue_this_month' => (float) ($salesThisMonth->revenue ?? 0),
                'currency' => config('crm.default_currency', 'USD'),
            ],
            'recent' => ProductSale::query()
                ->with(['product:id,name', 'company:id,name'])
                ->latest('sold_at')
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * @return array{pending_approval: int}|null
     */
    private function testimonialSnapshot(?Authenticatable $user): ?array
    {
        if (! $user?->can('cms.testimonials.view')) {
            return null;
        }

        return [
            'pending_approval' => Testimonial::query()
                ->where('status', CmsStatus::ARCHIVED->value)
                ->count(),
        ];
    }
}
