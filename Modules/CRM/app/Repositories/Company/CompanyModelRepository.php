<?php

namespace Modules\CRM\Repositories\Company;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\CRM\DTOs\Company\CompanyData;
use Modules\CRM\Models\Company;

class CompanyModelRepository implements CompanyRepository
{
    use ExceptionHandlerTrait;

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Company::query()
            ->with('user:id,name,email')
            ->filter($filters)
            ->latest()
            ->paginate($perPage);
    }

    public function findOrFail(int $id, bool $withTrashed = false): Company
    {
        $query = Company::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->findOrFail($id);
    }

    public function create(CompanyData $data): ?Company
    {
        return $this->execute(function () use ($data) {
            $company = Company::create($data->toArray());
            session()->flushMessage(true);

            return $company;
        });
    }

    public function update(Company $company, CompanyData $data): ?Company
    {
        return $this->execute(function () use ($company, $data) {
            $company->update($data->toArray());
            session()->flushMessage(true);

            return $company;
        });
    }

    public function delete(Company $company): ?bool
    {
        return $this->execute(function () use ($company) {
            $deleted = $company->delete();
            session()->flushMessage(true);

            return $deleted;
        });
    }

    public function bulkDelete(array $ids): ?bool
    {
        return $this->execute(function () use ($ids) {
            $deleted = Company::whereIn('id', $ids)->delete();
            session()->flushMessage(true);

            return (bool) $deleted;
        });
    }
}
