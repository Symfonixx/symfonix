<?php

namespace Modules\Cms\Repositories\Faq;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Cms\Models\Faq;

interface FaqRepository
{
    public function all(array $columns = ['*']): LengthAwarePaginator;

    public function find(int $id, array $columns = ['*']): ?Faq;

    public function store(array $data): mixed;

    public function update(array $data, Faq $faq): mixed;

    public function deleteMulti(array $ids): ?bool;
}
