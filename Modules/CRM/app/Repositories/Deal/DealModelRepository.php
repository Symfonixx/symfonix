<?php

namespace Modules\CRM\Repositories\Deal;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\CRM\DTOs\Deal\DealData;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\PipelineStage;

class DealModelRepository implements DealRepository
{
    use ExceptionHandlerTrait;

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Deal::query()
            ->visibleTo()
            ->with([
                'company:id,name',
                'pipelineStage:id,name,color',
                'assignee:id,name',
                'lead.tags',
            ])
            ->filter($filters)
            ->latest()
            ->paginate($perPage);
    }

    public function kanban(array $filters = []): Collection
    {
        $stages = PipelineStage::query()
            ->active()
            ->ordered()
            ->with(['deals' => function ($query) use ($filters) {
                $query
                    ->visibleTo()
                    ->with(['company:id,name', 'assignee:id,name', 'lead.tags'])
                    ->filter($filters)
                    ->latest();
            }])
            ->get();

        return $stages;
    }

    public function findOrFail(int $id, bool $withTrashed = false): Deal
    {
        $query = Deal::query()->visibleTo();

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->findOrFail($id);
    }

    public function create(DealData $data): ?Deal
    {
        return $this->execute(function () use ($data) {
            $deal = Deal::create($data->toArray());
            session()->flushMessage(true);

            return $deal;
        });
    }

    public function update(Deal $deal, DealData $data): ?Deal
    {
        return $this->execute(function () use ($deal, $data) {
            $deal->update($data->toArray());
            session()->flushMessage(true);

            return $deal;
        });
    }

    public function delete(Deal $deal): ?bool
    {
        return $this->execute(function () use ($deal) {
            $deleted = $deal->delete();
            session()->flushMessage(true);

            return $deleted;
        });
    }

    public function bulkDelete(array $ids): ?bool
    {
        return $this->execute(function () use ($ids) {
            $deleted = Deal::whereIn('id', $ids)->delete();
            session()->flushMessage(true);

            return (bool) $deleted;
        });
    }
}
