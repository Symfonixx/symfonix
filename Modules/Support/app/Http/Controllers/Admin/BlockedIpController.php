<?php

namespace Modules\Support\app\Http\Controllers\Admin;

use Akaunting\Firewall\Models\Ip as FirewallIp;
use App\Http\Controllers\Controller;

class BlockedIpController extends Controller
{
	public function __construct()
	{
		$this->setActive('support');
		$this->setActive('blocked_ips');
	}

	public function index()
	{
		$model = FirewallIp::blocked()->latest()->paginate(config('core.page_size'));

		return view('support::admin.blocked_ip.index', compact('model'));
	}
}





