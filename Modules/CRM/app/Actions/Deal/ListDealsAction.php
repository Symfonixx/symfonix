<?php

namespace Modules\CRM\Actions\Deal;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\CRM\Services\Deal\DealService;

class ListDealsAction
{
    public function __construct(private readonly DealService $service) {}

    public function execute(array $filters = []): LengthAwarePaginator
    {
        return $this->service->list($filters);
    }
}
