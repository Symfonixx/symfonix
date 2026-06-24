<?php

namespace Modules\CRM\Actions\Deal;

use Illuminate\Support\Collection;
use Modules\CRM\Services\Deal\DealService;

class ListDealsKanbanAction
{
    public function __construct(private readonly DealService $service) {}

    public function execute(array $filters = []): Collection
    {
        return $this->service->kanban($filters);
    }
}
