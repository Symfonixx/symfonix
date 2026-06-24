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
use Modules\Services\Models\Service;
use Modules\Support\Models\Subscriber;
use Monishroy\VisitorTracking\Helpers\Visitor;
use MonishRoy\VisitorTracking\Models\VisitorTable;

class DashboardController extends Controller
{
    public function index()
    {
        $this->setActive('dashboard');

        $visitorsStats = [
            'totalVisitorsCount' => VisitorTable::distinct('ip')->count('ip'),
            'todayVisitorsCount' => VisitorTable::whereDate('created_at', today())
                ->distinct('ip')->count('ip'),
        ];

        $topVisitedPages = Visitor::topVisitedPages();
        $stats = [
            'blogs' => Blog::count(),
            'leads' => Lead::count(),
            'customers' => User::where('type', 'customer')->count(),
            'employees' => User::where('type', 'employee')->count(),
            'subscribers' => Subscriber::count(),
            'contacts' => ContactForm::count(),
            'services' => Service::count(),
            'pages' => Page::count(),
        ];

        $crmStats = null;
        if (auth()->user()?->can('CRM Management')) {
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
        }

        return view('user::admin.dashboard.index', compact('stats', 'visitorsStats', 'topVisitedPages', 'crmStats'));
    }
}
