<?php

namespace Modules\Project\Repositories\Project;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\Project\DTOs\Project\ProjectData;
use Modules\Project\Models\Project;

class ProjectModelRepository implements ProjectRepository
{
    use ExceptionHandlerTrait;

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Project::query()
            ->with([
                'company:id,name',
                'status:id,name,color_code',
                'deal:id,title',
            ])
            ->filter($filters)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findOrFail(int $id): Project
    {
        return Project::query()->findOrFail($id);
    }

    public function create(ProjectData $data): ?Project
    {
        return $this->execute(function () use ($data) {
            $payload = $data->toArray();
            $currency = strtoupper((string) ($payload['currency']
                ?? app(\Modules\Finance\Services\CurrencyService::class)->defaultCurrency()));
            $payload['currency'] = $currency;
            $payload['budget_exchange_rate'] = app(\Modules\Finance\Services\CurrencyService::class)
                ->snapshotRateToBase($currency);

            $project = Project::create($payload);
            session()->flushMessage(true);

            return $project;
        });
    }

    public function update(Project $project, ProjectData $data): ?Project
    {
        return $this->execute(function () use ($project, $data) {
            $payload = $data->toArray();
            $currency = strtoupper((string) ($payload['currency'] ?? $project->currency
                ?? app(\Modules\Finance\Services\CurrencyService::class)->defaultCurrency()));
            $payload['currency'] = $currency;

            if ($project->currency !== $currency || (float) $project->budget !== (float) ($payload['budget'] ?? null)) {
                $payload['budget_exchange_rate'] = app(\Modules\Finance\Services\CurrencyService::class)
                    ->snapshotRateToBase($currency);
            }

            $project->update($payload);
            session()->flushMessage(true);

            return $project;
        });
    }

    public function delete(Project $project): ?bool
    {
        return $this->execute(function () use ($project) {
            $deleted = $project->delete();
            session()->flushMessage(true);

            return $deleted;
        });
    }

    public function bulkDelete(array $ids): ?bool
    {
        return $this->execute(function () use ($ids) {
            Project::destroy($ids);
            session()->flushMessage(true);

            return true;
        });
    }
}
