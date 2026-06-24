<?php

namespace Modules\CRM\Actions\Company;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\CRM\Services\Company\CompanyService;

class ListCompaniesAction
{
    public function __construct(private readonly CompanyService $service) {}

    public function execute(array $filters = []): LengthAwarePaginator
    {
        return $this->service->list($filters);
    }
}
