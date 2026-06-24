<?php

namespace Modules\CRM\Repositories\Company;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\CRM\DTOs\Company\CompanyData;
use Modules\CRM\Models\Company;

interface CompanyRepository
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findOrFail(int $id, bool $withTrashed = false): Company;

    public function create(CompanyData $data): ?Company;

    public function update(Company $company, CompanyData $data): ?Company;

    public function delete(Company $company): ?bool;

    public function bulkDelete(array $ids): ?bool;
}
