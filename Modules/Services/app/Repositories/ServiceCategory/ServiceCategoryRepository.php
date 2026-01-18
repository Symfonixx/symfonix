<?php

namespace Modules\Services\Repositories\ServiceCategory;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Services\Models\ServiceCategory;

interface ServiceCategoryRepository
{
    public function all(array $columns = ['*']): LengthAwarePaginator;

    public function find(int $id, array $columns = ['*']): ?ServiceCategory;

    public function store(array $data): mixed;

    public function update(array $data, ServiceCategory $category): mixed;

    public function deleteMulti(array $ids): ?bool;
}




