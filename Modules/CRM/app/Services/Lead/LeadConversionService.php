<?php

namespace Modules\CRM\Services\Lead;

use Modules\CRM\Models\Lead;
use Illuminate\Support\Facades\DB;
use Modules\CRM\DTOs\Deal\DealData;
use Modules\CRM\Models\Deal;
use Modules\CRM\Repositories\PipelineStage\PipelineStageRepository;
use Modules\CRM\Services\Company\CompanyProvisionerService;
use Modules\CRM\Services\Contact\ContactService;
use Modules\CRM\Services\Deal\DealService;
use Modules\CRM\Support\AuditLogger;
use Modules\CRM\Models\CrmAuditLog;
use Modules\User\Support\EmployeeAccess;

class LeadConversionService
{
    public function __construct(
        private readonly DealService $dealService,
        private readonly PipelineStageRepository $stageRepository,
        private readonly CompanyProvisionerService $companyProvisioner,
        private readonly ContactService $contactService,
    ) {}

    public function convert(Lead $lead, array $options = []): Deal
    {
        if ($lead->deal_id) {
            abort(422, __('crm::lead.conversion.already_converted'));
        }

        return DB::transaction(function () use ($lead, $options) {
            $lead->loadMissing(['company', 'service', 'services:services.id,title']);

            $this->companyProvisioner->provisionFromLead($lead);
            $this->contactService->findOrCreateFromLead($lead->fresh());
            $lead->refresh();

            $title = $options['title'] ?? $this->buildTitle($lead);
            $stageId = (int) ($options['pipeline_stage_id'] ?? $this->stageRepository->findDefault()?->id);
            $assignedTo = $options['assigned_to'] ?? $lead->assigned_to ?? EmployeeAccess::idForUser(auth()->user());

            $description = $this->buildDescription($lead);

            $dealData = DealData::from([
                'title' => $title,
                'company_id' => $lead->company_id,
                'pipeline_stage_id' => $stageId,
                'assigned_to' => $assignedTo,
                'value' => $this->parseBudget($lead->project_budget),
                'currency' => $options['currency'] ?? 'USD',
                'probability' => null,
                'expected_close_date' => $options['expected_close_date'] ?? null,
                'source' => $lead->source ?? Lead::SOURCE_WEBSITE,
                'description' => $description,
                'lost_reason' => null,
                'status' => Deal::STATUS_OPEN,
            ]);

            $deal = $this->dealService->create($dealData);

            if (! $deal) {
                abort(500, __('crm::lead.conversion.failed'));
            }

            $deal->update(['lead_id' => $lead->id]);

            // Carry the lead's services onto the deal as line items.
            $leadServiceIds = $lead->serviceIds();
            if ($leadServiceIds !== []) {
                $this->dealService->syncServices($deal, array_map(
                    fn (int $serviceId) => ['service_id' => $serviceId, 'quantity' => 1, 'unit_price' => 0],
                    $leadServiceIds
                ));
            }

            $lead->update([
                'deal_id' => $deal->id,
                'converted_at' => now(),
                'assigned_to' => $assignedTo,
            ]);

            AuditLogger::log($lead, CrmAuditLog::EVENT_CONVERTED, __('crm::timeline.audit.converted'));
            AuditLogger::log($deal, CrmAuditLog::EVENT_CONVERTED, __('crm::timeline.audit.converted'), null, ['lead_id' => $lead->id]);

            session()->flushMessage(true);

            return $deal->fresh(['company', 'pipelineStage', 'assignee']);
        });
    }

    private function buildTitle(Lead $lead): string
    {
        if ($lead->service_interest) {
            return $lead->service_interest;
        }

        if ($lead->services->isNotEmpty()) {
            return $lead->services
                ->map(fn ($service) => $service->getTranslation('title', app()->getLocale()))
                ->implode(', ');
        }

        if ($lead->service) {
            return $lead->service->getTranslation('title', app()->getLocale());
        }

        if ($lead->name) {
            return __('crm::lead.conversion.default_title', ['name' => $lead->name]);
        }

        return __('crm::lead.conversion.fallback_title', ['id' => $lead->id]);
    }

    private function buildDescription(Lead $lead): string
    {
        $parts = array_filter([
            $lead->problem_statement,
            $lead->company_name ? __('crm::lead.fields.company_name').': '.$lead->company_name : null,
            $lead->project_budget ? __('crm::lead.fields.project_budget').': '.$lead->project_budget : null,
        ]);

        return implode("\n\n", $parts);
    }

    private function parseBudget(?string $budget): ?float
    {
        if (! $budget) {
            return null;
        }

        if (preg_match('/[\d,.]+/', $budget, $matches)) {
            $value = (float) str_replace(',', '', $matches[0]);

            return $value > 0 ? $value : null;
        }

        return null;
    }
}
