<?php

namespace Modules\Support\app\Http\Controllers\Admin;

use Akaunting\Firewall\Models\Ip as FirewallIp;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Support\Models\Subscriber;
use Modules\Support\Models\ContactForm;

class FirewallController extends Controller
{
	/**
	 * Manually block an IP address using akaunting/laravel-firewall.
	 */
	public function block(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'ip' => ['required', 'ip'],
		]);

		FirewallIp::updateOrCreate(
			['ip' => $data['ip']],
			['blocked' => 1]
		);

		// Mark matching records as blocked in our domain models
		Subscriber::where('ip_address', $data['ip'])->update(['blocked' => 1]);
		ContactForm::where('ip_address', $data['ip'])->update(['blocked' => 1]);

		session()->flushMessage(true, __('IP address has been blocked successfully.'));

		return redirect()->back();
	}

	/**
	 * Manually unblock an IP address.
	 */
	public function unblock(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'ip' => ['required', 'ip'],
		]);

		FirewallIp::where('ip', $data['ip'])->update(['blocked' => 0]);

		Subscriber::where('ip_address', $data['ip'])->update(['blocked' => 0]);
		ContactForm::where('ip_address', $data['ip'])->update(['blocked' => 0]);

		session()->flushMessage(true, __('IP address has been unblocked successfully.'));

		return redirect()->back();
	}
}


