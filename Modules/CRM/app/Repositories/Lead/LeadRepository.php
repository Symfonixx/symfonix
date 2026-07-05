<?php

namespace Modules\CRM\Repositories\Lead;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\CRM\DTOs\Lead\LeadData;
use Modules\CRM\Models\Lead;

interface LeadRepository
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findOrFail(int $id): Lead;

    public function create(LeadData $data): ?Lead;

    public function update(Lead $lead, LeadData $data): ?Lead;

    public function delete(Lead $lead): ?bool;

    public function bulkDelete(array $ids): ?bool;

    public function setBlocked(Lead $lead, bool $blocked): ?Lead;
}
