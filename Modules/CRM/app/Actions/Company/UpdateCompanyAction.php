<?php

namespace Modules\CRM\Actions\Company;

use Modules\CRM\DTOs\Company\CompanyData;
use Modules\CRM\Models\Company;
use Modules\CRM\Services\Company\CompanyService;

class UpdateCompanyAction
{
    public function __construct(private readonly CompanyService $service) {}

    public function execute(Company $company, CompanyData $data): ?Company
    {
        return $this->service->update($company, $data);
    }
}
