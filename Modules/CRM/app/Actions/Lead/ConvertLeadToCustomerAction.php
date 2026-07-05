<?php

namespace Modules\CRM\Actions\Lead;

use Modules\CRM\Models\Company;
use Modules\CRM\Models\Lead;
use Modules\CRM\Services\Company\CompanyProvisionerService;
use Modules\CRM\Services\Contact\ContactService;
use Modules\CRM\Support\AuditLogger;
use Modules\CRM\Models\CrmAuditLog;

class ConvertLeadToCustomerAction
{
    public function __construct(
        private readonly CompanyProvisionerService $companyProvisioner,
        private readonly ContactService $contactService,
    ) {}

    public function execute(Lead $lead): Company
    {
        if (! $lead->company_name && ! $lead->company_id) {
            abort(422, __('crm::lead.conversion.customer_requires_company'));
        }

        $company = $this->companyProvisioner->provisionFromLead($lead);

        if (! $company) {
            abort(422, __('crm::lead.conversion.customer_failed'));
        }

        $this->contactService->findOrCreateFromLead($lead->fresh());

        $lead->update([
            'status' => Lead::STATUS_CONVERTED,
            'converted_at' => $lead->converted_at ?? now(),
        ]);

        AuditLogger::log($lead, CrmAuditLog::EVENT_CONVERTED, __('crm::lead.conversion.converted_to_customer'));
        session()->flushMessage(true);

        return $company->fresh(['user']);
    }
}
