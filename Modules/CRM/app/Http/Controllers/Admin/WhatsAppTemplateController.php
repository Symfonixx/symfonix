<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\CRM\Http\Requests\StoreWhatsAppTemplateRequest;
use Modules\CRM\Http\Requests\UpdateWhatsAppTemplateRequest;
use Modules\CRM\Models\WhatsAppTemplate;
use Modules\CRM\Services\Marketing\WhatsAppTemplateService;

class WhatsAppTemplateController extends Controller
{
    public function __construct(
        private readonly WhatsAppTemplateService $templateService,
    ) {
        $this->setActive('crm');
        $this->setActive('marketing');
    }

    public function index(): View
    {
        $templates = WhatsAppTemplate::query()
            ->with('user:id,name')
            ->latest()
            ->paginate(config('core.page_size'));

        return view('crm::admin.marketing.whatsapp.templates.index', compact('templates'));
    }

    public function create(): View
    {
        return view('crm::admin.marketing.whatsapp.templates.create');
    }

    public function store(StoreWhatsAppTemplateRequest $request): RedirectResponse
    {
        $this->templateService->create(
            $request->validated(),
            (int) $request->user()->id,
        );

        session()->flushMessage(true, __('crm::whatsapp.messages.template_created'));

        return redirect()->route('admin.crm.marketing.whatsapp-templates.index');
    }

    public function edit(WhatsAppTemplate $template): View
    {
        return view('crm::admin.marketing.whatsapp.templates.edit', compact('template'));
    }

    public function update(UpdateWhatsAppTemplateRequest $request, WhatsAppTemplate $template): RedirectResponse
    {
        $this->templateService->update($template, $request->validated());

        session()->flushMessage(true, __('crm::whatsapp.messages.template_updated'));

        return redirect()->route('admin.crm.marketing.whatsapp-templates.index');
    }

    public function destroy(WhatsAppTemplate $template): RedirectResponse
    {
        if ($template->campaigns()->exists()) {
            session()->flushMessage(false, __('crm::whatsapp.messages.template_in_use'));

            return back();
        }

        $this->templateService->delete($template);

        session()->flushMessage(true, __('crm::whatsapp.messages.template_deleted'));

        return redirect()->route('admin.crm.marketing.whatsapp-templates.index');
    }
}
