<?php

namespace Modules\CRM\Repositories\Deal;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\CRM\DTOs\Deal\DealData;
use Modules\CRM\Models\Deal;

interface DealRepository
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function kanban(array $filters = []): Collection;

    public function findOrFail(int $id, bool $withTrashed = false): Deal;

    public function create(DealData $data): ?Deal;

    public function update(Deal $deal, DealData $data): ?Deal;

    public function delete(Deal $deal): ?bool;

    public function bulkDelete(array $ids): ?bool;
}
