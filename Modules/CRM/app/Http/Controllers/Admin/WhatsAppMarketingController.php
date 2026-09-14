<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Base\Support\WhatsAppConfig;
use Modules\CRM\Actions\Marketing\SendWhatsAppCampaignAction;
use Modules\CRM\Http\Requests\SendWhatsAppCampaignRequest;
use Modules\CRM\Models\Contact;
use Modules\CRM\Models\ContactForm;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\WhatsAppCampaign;
use Modules\CRM\Models\WhatsAppTemplate;
use Modules\CRM\Services\Marketing\WhatsAppTemplateService;

class WhatsAppMarketingController extends Controller
{
    public function __construct(
        private readonly SendWhatsAppCampaignAction $sendWhatsAppCampaignAction,
        private readonly WhatsAppTemplateService $templateService,
    ) {
        $this->setActive('crm');
        $this->setActive('marketing');
    }

    public function create(): View
    {
        return view('crm::admin.marketing.whatsapp.create', $this->formData());
    }

    public function store(SendWhatsAppCampaignRequest $request): RedirectResponse
    {
        try {
            $campaign = $this->sendWhatsAppCampaignAction->execute(
                $request->validated(),
                (int) $request->user()->id,
            );

            session()->flushMessage(
                true,
                __('crm::whatsapp.messages.queued', ['count' => $campaign->recipients_count]),
            );
        } catch (\InvalidArgumentException $e) {
            session()->flushMessage(false, $e->getMessage());

            return back()->withInput();
        } catch (\Throwable $e) {
            report($e);
            session()->flushMessage(false, __('crm::whatsapp.messages.send_failed'));

            return back()->withInput();
        }

        return redirect()->route('admin.crm.marketing.index', ['channel' => 'whatsapp']);
    }

    public function show(WhatsAppCampaign $campaign): View
    {
        $campaign->loadMissing(['user:id,name', 'template', 'messageLogs']);

        return view('crm::admin.marketing.whatsapp.show', compact('campaign'));
    }

    public function templateVariables(WhatsAppTemplate $template): JsonResponse
    {
        $variables = $this->templateService->parseBodyVariables($template->body);

        return response()->json([
            'id' => $template->id,
            'name' => $template->name,
            'language' => $template->language,
            'body' => $template->body,
            'header_type' => $template->header_type,
            'header_content' => $template->header_content,
            'footer' => $template->footer,
            'buttons' => $template->buttons ?? [],
            'variables' => collect($variables)->map(fn (int $num) => [
                'index' => $num,
                'placeholder' => '{{'.$num.'}}',
            ])->values(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'whatsappConfigured' => WhatsAppConfig::isConfigured(),
            'templates' => $this->templateService->listSendable(),
            'leads' => Lead::query()
                ->where('blocked', false)
                ->whereNotNull('phone')
                ->select(['id', 'name', 'phone'])
                ->orderBy('name')
                ->get(),
            'contacts' => Contact::query()
                ->where(function ($query) {
                    $query->whereNotNull('phone')->orWhereNotNull('phone2');
                })
                ->select(['id', 'name', 'phone', 'phone2'])
                ->orderBy('name')
                ->get(),
            'deals' => Deal::query()
                ->with(['lead:id,name,phone', 'company:id,name,phone'])
                ->select(['id', 'title', 'lead_id', 'company_id'])
                ->latest()
                ->limit(500)
                ->get(),
            'contactForms' => ContactForm::query()
                ->where('blocked', false)
                ->whereNotNull('mobile')
                ->select(['id', 'name', 'mobile', 'subject'])
                ->latest()
                ->limit(500)
                ->get(),
        ];
    }
}
