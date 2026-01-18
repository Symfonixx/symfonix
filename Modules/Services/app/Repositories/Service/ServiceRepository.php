<?php

namespace Modules\Services\Repositories\Service;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Services\Models\Service;

interface ServiceRepository
{
    public function all(array $columns = ['*']): LengthAwarePaginator;

    public function find(int $id, array $columns = ['*']): ?Service;

    public function store(array $data): mixed;

    public function update(array $data, Service $service): mixed;

    public function deleteMulti(array $ids): ?bool;
}
