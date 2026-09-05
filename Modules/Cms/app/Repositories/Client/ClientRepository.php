<?php

namespace Modules\Cms\Repositories\Client;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Cms\Models\Client;

interface ClientRepository
{
    public function all(array $columns = ['*']): LengthAwarePaginator;

    public function find(int $id, array $columns = ['*']): ?Client;

    public function store(array $data): mixed;

    public function update(array $data, Client $client): mixed;

    public function deleteMulti(array $ids): ?bool;
}
