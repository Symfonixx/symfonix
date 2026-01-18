<?php

namespace Modules\User\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Modules\Cms\Models\Blog;
use Modules\Cms\Models\Page;
use Modules\Services\Models\Service;

use Modules\Support\Models\ContactForm;
use Modules\Support\Models\Subscriber;
use Monishroy\VisitorTracking\Helpers\Visitor;
use MonishRoy\VisitorTracking\Models\VisitorTable;

class DashboardController extends Controller {
    public function index() {
        $this->setActive('dashboard');

        $visitorsStats = [
            'totalVisitorsCount' => VisitorTable::distinct('ip')->count('ip'),
            'todayVisitorsCount' => VisitorTable::whereDate('created_at', today())
                ->distinct('ip')->count('ip'),
        ];

        $topVisitedPages = Visitor::topVisitedPages();
        $stats = [
            'blogs' => Blog::count(),
            'users' => User::where('type', 'user')->count(),
            'admins' => User::where('type', 'admin')->count(),
            'subscribers' => Subscriber::count(),
            'contacts' => ContactForm::count(),
            'services' => Service::count(),
            'pages' => Page::count(),
        ];

        return view('user::admin.dashboard.index', compact('stats', 'visitorsStats' , 'topVisitedPages'));
    }
}
