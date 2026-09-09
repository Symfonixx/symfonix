<?php

namespace Modules\CRM\Repositories\Lead;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\CRM\DTOs\Lead\LeadData;
use Modules\CRM\Models\Lead;

class LeadModelRepository implements LeadRepository
{
    use ExceptionHandlerTrait;

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Lead::query()
            ->with(['company:id,name', 'service:id,title', 'assignee:id,name', 'tags'])
            ->filter($filters)
            ->latest()
            ->paginate($perPage);
    }

    public function findOrFail(int $id): Lead
    {
        return Lead::query()->findOrFail($id);
    }

    public function create(LeadData $data): ?Lead
    {
        return $this->execute(function () use ($data) {
            $lead = Lead::create($data->toArray());
            session()->flushMessage(true);

            return $lead;
        });
    }

    public function update(Lead $lead, LeadData $data): ?Lead
    {
        return $this->execute(function () use ($lead, $data) {
            $lead->update($data->toArray());
            session()->flushMessage(true);

            return $lead;
        });
    }

    public function delete(Lead $lead): ?bool
    {
        return $this->execute(function () use ($lead) {
            $deleted = $lead->delete();
            session()->flushMessage(true);

            return $deleted;
        });
    }

    public function bulkDelete(array $ids): ?bool
    {
        return $this->execute(function () use ($ids) {
            Lead::destroy($ids);
            session()->flushMessage(true);

            return true;
        });
    }

    public function setBlocked(Lead $lead, bool $blocked): ?Lead
    {
        return $this->execute(function () use ($lead, $blocked) {
            $lead->update(['blocked' => $blocked]);
            session()->flushMessage(true);

            return $lead;
        });
    }
}
