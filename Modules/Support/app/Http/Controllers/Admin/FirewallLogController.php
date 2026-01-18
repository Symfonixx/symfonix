<?php

namespace Modules\Support\app\Http\Controllers\Admin;

use Akaunting\Firewall\Models\Log as FirewallLog;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Support\app\Exports\FirewallLogExport;

class FirewallLogController extends Controller
{
	public function __construct()
	{
		$this->setActive('support');
		$this->setActive('firewall_logs');
	}

	public function index()
	{
		$model = FirewallLog::latest()->paginate(config('core.page_size'));

		return view('support::admin.firewall_log.index', compact('model'));
	}

	public function export()
	{
		return Excel::download(new FirewallLogExport, 'firewall_logs_' . date('Y-m-d_His') . '.xlsx');
	}
}



