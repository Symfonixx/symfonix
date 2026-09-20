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
use Modules\CRM\Models\LeadTag;
use Modules\CRM\Models\WhatsAppCampaign;
use Modules\CRM\Models\WhatsAppTemplate;
use Modules\CRM\Services\Marketing\MarketingGroupService;
use Modules\CRM\Services\Marketing\WhatsAppTemplateService;

class WhatsAppMarketingController extends Controller
{
    public function __construct(
        private readonly SendWhatsAppCampaignAction $sendWhatsAppCampaignAction,
        private readonly WhatsAppTemplateService $templateService,
        private readonly MarketingGroupService $marketingGroupService,
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

        return redirect()->route(
            $campaign->marketing_group_id
                ? 'admin.crm.marketing.groups.show'
                : 'admin.crm.marketing.index',
            $campaign->marketing_group_id
                ? $campaign->marketing_group_id
                : ['channel' => 'whatsapp'],
        );
    }

    public function show(WhatsAppCampaign $campaign): View
    {
        $campaign->loadMissing(['user:id,name', 'template', 'messageLogs', 'group:id,title,goal']);

        $parameterItems = $campaign->template
            ? $this->templateService->describeParameters($campaign->template_parameters ?? [], $campaign->template)
            : [];

        return view('crm::admin.marketing.whatsapp.show', compact('campaign', 'parameterItems'));
    }

    public function templateVariables(WhatsAppTemplate $template): JsonResponse
    {
        return response()->json([
            'id' => $template->id,
            'name' => $template->name,
            'language' => $template->language,
            'body' => $template->body,
            'header_type' => $template->header_type,
            'header_content' => $template->header_content,
            'footer' => $template->footer,
            'buttons' => $template->buttons ?? [],
            'variables' => $this->templateService->collectVariables($template),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'whatsappConfigured' => WhatsAppConfig::isConfigured(),
            'marketingGroups' => $this->marketingGroupService->formOptions(),
            'selectedGroupId' => old('marketing_group_id', request('group')),
            'templates' => $this->templateService->listSendable(),
            'leadTags' => LeadTag::optionsForMarketing('whatsapp'),
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
