<?php

namespace Modules\CRM\Actions\Company;

use Modules\CRM\Services\Company\CompanyService;

class BulkDeleteCompaniesAction
{
    public function __construct(private readonly CompanyService $service) {}

    public function execute(array $ids): ?bool
    {
        return $this->service->bulkDelete($ids);
    }
}
