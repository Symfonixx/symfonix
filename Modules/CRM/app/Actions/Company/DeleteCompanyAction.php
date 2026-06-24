<?php

namespace Modules\CRM\Actions\Company;

use Modules\CRM\Models\Company;
use Modules\CRM\Services\Company\CompanyService;

class DeleteCompanyAction
{
    public function __construct(private readonly CompanyService $service) {}

    public function execute(Company $company): ?bool
    {
        return $this->service->delete($company);
    }
}
