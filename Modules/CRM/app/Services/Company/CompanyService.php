<?php

namespace Modules\CRM\Services\Company;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Modules\CRM\DTOs\Company\CompanyData;
use Modules\CRM\Models\Company;
use Modules\CRM\Repositories\Company\CompanyRepository;
use Modules\CRM\Support\AuditLogger;

class CompanyService
{
    public function __construct(private readonly CompanyRepository $repository) {}

    public function list(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, (int) config('core.page_size', 15));
    }

    public function findForEdit(int $id, bool $withTrashed = false): Company
    {
        return $this->repository->findOrFail($id, $withTrashed);
    }

    public function create(CompanyData $data): ?Company
    {
        $company = $this->repository->create($data);

        if ($company) {
            AuditLogger::logCreated($company);

            Log::info('CRM company created', [
                'company_id' => $company->id,
                'name' => $company->name,
                'actor_id' => auth()->id(),
            ]);
        }

        return $company;
    }

    public function update(Company $company, CompanyData $data): ?Company
    {
        $before = AuditLogger::auditableSnapshot($company);
        $updated = $this->repository->update($company, $data);

        if ($updated) {
            AuditLogger::logUpdated($company, $before, AuditLogger::auditableSnapshot($company->fresh()));

            Log::info('CRM company updated', [
                'company_id' => $company->id,
                'name' => $company->name,
                'actor_id' => auth()->id(),
            ]);
        }

        return $updated;
    }

    public function delete(Company $company): ?bool
    {
        AuditLogger::logDeleted($company);
        $deleted = $this->repository->delete($company);

        if ($deleted) {
            Log::warning('CRM company deleted', [
                'company_id' => $company->id,
                'name' => $company->name,
                'actor_id' => auth()->id(),
            ]);
        }

        return $deleted;
    }

    public function bulkDelete(array $ids): ?bool
    {
        $deleted = $this->repository->bulkDelete($ids);

        if ($deleted) {
            Log::warning('CRM companies bulk deleted', [
                'company_ids' => $ids,
                'actor_id' => auth()->id(),
            ]);
        }

        return $deleted;
    }
}
