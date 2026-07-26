<?php

namespace Modules\User\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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
use Modules\Support\Models\Subscriber;
use Modules\Support\Models\Ticket;
use Modules\Testimonial\Models\Testimonial;
use Modules\User\app\Repositories\Employee\EmployeeRepository;
use Monishroy\VisitorTracking\Helpers\Visitor;
use MonishRoy\VisitorTracking\Models\VisitorTable;

class DashboardController extends Controller
{
    public function index(EmployeeRepository $employeeRepository, FinanceService $financeService)
    {
        $this->setActive('dashboard');

        $user = auth()->user();

        $visitorsStats = [
            'totalVisitorsCount' => VisitorTable::distinct('ip')->count('ip'),
            'todayVisitorsCount' => VisitorTable::whereDate('created_at', today())
                ->distinct('ip')->count('ip'),
            'weekVisitorsCount' => VisitorTable::where('created_at', '>=', now()->startOfWeek())
                ->distinct('ip')->count('ip'),
        ];

        $topVisitedPages = Visitor::topVisitedPages(10);
        $topReferrers = Visitor::topReferrers(10);
        $visitsByMonth = Visitor::visitsByMonth(12);
        $stats = [
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

        $notificationStats = [
            'unread' => $user?->unreadNotifications()->count() ?? 0,
        ];

        $recentNotifications = $user
            ? $user->notifications()->latest()->limit(5)->get()
            : collect();

        $recentContacts = ContactForm::query()->latest()->limit(5)->get();

        $crmStats = null;
        $leadStats = null;
        $recentLeads = collect();
        if ($user?->can('CRM Management')) {
            $crmStats = [
                'companies' => Company::query()->count(),
                'deals' => Deal::query()->visibleTo()->count(),
                'open_deals' => Deal::query()->visibleTo()->where('status', Deal::STATUS_OPEN)->count(),
                'pipeline_value' => (float) Deal::query()->visibleTo()->where('status', Deal::STATUS_OPEN)->sum('value'),
                'won_this_month' => Deal::query()->visibleTo()
                    ->where('status', Deal::STATUS_WON)
                    ->whereBetween('won_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->count(),
                'currency' => config('crm.default_currency', 'USD'),
            ];

            $leadStats = [
                'new' => Lead::query()->where('status', Lead::STATUS_NEW)->count(),
                'this_month' => Lead::query()
                    ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->count(),
            ];

            $recentLeads = Lead::query()
                ->with('assignee:id,name')
                ->latest()
                ->limit(5)
                ->get();
        }

        $ticketStats = null;
        $recentTickets = collect();
        if ($user?->can('Support Management')) {
            $ticketStats = [
                'total' => Ticket::query()->count(),
                'open' => Ticket::query()
                    ->whereIn('status', [Ticket::STATUS_OPEN, Ticket::STATUS_IN_PROGRESS])
                    ->count(),
                'resolved' => Ticket::query()->where('status', Ticket::STATUS_RESOLVED)->count(),
                'closed' => Ticket::query()->where('status', Ticket::STATUS_CLOSED)->count(),
            ];

            $recentTickets = Ticket::query()
                ->with(['user:id,name,email', 'category:id,name'])
                ->latest()
                ->limit(5)
                ->get();
        }

        $financeStats = null;
        $recentInvoices = collect();
        if ($user?->can('Finance Management')) {
            $monthSummary = $financeService->getMetricsSummary([now()->format('Y-m')]);
            $receivable = $financeService->getAccountsReceivableAging();

            $financeStats = [
                'revenue_this_month' => $monthSummary['revenue'],
                'outstanding' => $receivable['total_outstanding'],
                'open_invoices' => Invoice::query()->open()->count(),
                'paid_this_month' => (float) Invoice::query()
                    ->where('status', Invoice::STATUS_PAID)
                    ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->sum('total'),
                'currency' => $receivable['currency'],
            ];

            $recentInvoices = Invoice::query()
                ->with('company:id,name')
                ->latest()
                ->limit(5)
                ->get();
        }

        $projectStats = null;
        $recentProjects = collect();
        if ($user?->can('Project Management')) {
            $projectStats = [
                'total' => Project::query()->count(),
                'active' => Project::query()
                    ->whereHas('status', fn ($query) => $query->where('name', '!=', 'Completed'))
                    ->count(),
                'unpaid' => Project::query()
                    ->whereIn('payment_status', [Project::PAYMENT_UNPAID, Project::PAYMENT_PARTIALLY_PAID])
                    ->count(),
            ];

            $recentProjects = Project::query()
                ->with(['company:id,name', 'status:id,name,color_code'])
                ->latest()
                ->limit(5)
                ->get();
        }

        $testimonialStats = null;
        if ($user?->can('Testimonials Management')) {
            $testimonialStats = [
                'pending_approval' => Testimonial::query()
                    ->where('status', 'Archived')
                    ->count(),
            ];
        }

        $productStats = null;
        $recentProductSales = collect();
        if ($user?->can('Product Management')) {
            $productStats = [
                'total' => Product::query()->count(),
                'published' => Product::query()->where('is_published', true)->count(),
                'sales_this_month' => ProductSale::query()
                    ->whereBetween('sold_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->count(),
                'revenue_this_month' => (float) ProductSale::query()
                    ->whereBetween('sold_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->sum('total_amount'),
                'currency' => config('crm.default_currency', 'USD'),
            ];

            $recentProductSales = ProductSale::query()
                ->with(['product:id,name', 'company:id,name'])
                ->latest('sold_at')
                ->limit(5)
                ->get();
        }

        return view('user::admin.dashboard.index', compact(
            'stats',
            'visitorsStats',
            'topVisitedPages',
            'topReferrers',
            'visitsByMonth',
            'crmStats',
            'ticketStats',
            'recentTickets',
            'financeStats',
            'recentInvoices',
            'projectStats',
            'recentProjects',
            'productStats',
            'recentProductSales',
            'recentContacts',
            'leadStats',
            'recentLeads',
            'notificationStats',
            'recentNotifications',
            'testimonialStats',
        ));
    }
}
