<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\CRM\Actions\Marketing\SendMarketingEmailAction;
use Modules\CRM\Http\Requests\SendMarketingEmailRequest;
use Modules\CRM\Models\Contact;
use Modules\CRM\Models\ContactForm;
use Modules\CRM\Models\MarketingCampaign;
use Modules\CRM\Models\WhatsAppCampaign;
use Modules\Support\Models\Subscriber;

class MarketingController extends Controller
{
    public function __construct(
        private readonly SendMarketingEmailAction $sendMarketingEmailAction,
    ) {
        $this->setActive('crm');
        $this->setActive('marketing');
    }

    public function index(): View|RedirectResponse
    {
        $channel = request('channel', 'email');
        $user = auth()->user();

        if ($channel === 'whatsapp') {
            abort_unless($user?->can('marketing.whatsapp.view'), 403);
            $model = WhatsAppCampaign::query()
                ->with(['user:id,name', 'template:id,name,language'])
                ->latest()
                ->paginate(config('core.page_size'))
                ->appends(['channel' => 'whatsapp']);
        } else {
            if (! $user?->can('marketing.email.view') && $user?->can('marketing.whatsapp.view')) {
                return redirect()->route('admin.crm.marketing.index', ['channel' => 'whatsapp']);
            }

            abort_unless($user?->can('marketing.email.view'), 403);
            $channel = 'email';
            $model = MarketingCampaign::query()
                ->with('user:id,name')
                ->latest()
                ->paginate(config('core.page_size'))
                ->appends(['channel' => 'email']);
        }

        return view('crm::admin.marketing.index', compact('model', 'channel'));
    }

    public function create()
    {
        return view('crm::admin.marketing.create', $this->formData());
    }

    public function store(SendMarketingEmailRequest $request): RedirectResponse
    {
        try {
            $campaign = $this->sendMarketingEmailAction->execute(
                $request->validated(),
                (int) $request->user()->id,
            );

            session()->flushMessage(
                true,
                __('crm::marketing.messages.queued', ['count' => $campaign->recipients_count]),
            );
        } catch (\InvalidArgumentException $e) {
            session()->flushMessage(false, $e->getMessage());

            return back()->withInput();
        } catch (\Throwable $e) {
            report($e);
            session()->flushMessage(false, __('crm::marketing.messages.send_failed'));

            return back()->withInput();
        }

        return redirect()->route('admin.crm.marketing.index');
    }

    public function show(MarketingCampaign $marketing)
    {
        $marketing->loadMissing('user:id,name');

        return view('crm::admin.marketing.show', ['campaign' => $marketing]);
    }

    private function formData(): array
    {
        return [
            'subscribers' => Subscriber::query()
                ->where('blocked', false)
                ->whereNotNull('email')
                ->select(['id', 'email'])
                ->orderBy('email')
                ->get(),
            'contacts' => Contact::query()
                ->whereNotNull('email')
                ->select(['id', 'name', 'email'])
                ->orderBy('name')
                ->get(),
            'contactForms' => ContactForm::query()
                ->where('blocked', false)
                ->whereNotNull('email')
                ->select(['id', 'name', 'email', 'subject'])
                ->latest()
                ->limit(500)
                ->get(),
        ];
    }
}
