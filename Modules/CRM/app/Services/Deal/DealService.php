<?php

namespace Modules\CRM\Services\Deal;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\CRM\Events\DealStageChanged;
use Modules\CRM\DTOs\Deal\DealData;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\DealStageHistory;
use Modules\CRM\Models\PipelineStage;
use Modules\CRM\Repositories\Deal\DealRepository;
use Modules\CRM\Repositories\PipelineStage\PipelineStageRepository;
use Modules\CRM\Support\AuditLogger;

class DealService
{
    public function __construct(
        private readonly DealRepository $repository,
        private readonly PipelineStageRepository $stageRepository,
    ) {}

    public function list(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, (int) config('core.page_size', 15));
    }

    public function kanban(array $filters = []): Collection
    {
        return $this->repository->kanban($filters);
    }

    public function findForEdit(int $id, bool $withTrashed = false): Deal
    {
        return $this->repository->findOrFail($id, $withTrashed);
    }

    public function create(DealData $data, array $services = []): ?Deal
    {
        $deal = $this->repository->create($data);

        if ($deal) {
            $this->syncServices($deal, $services);
            $stage = $this->stageRepository->findOrFail($deal->pipeline_stage_id);
            $this->applyStageOutcome($deal, $stage);

            if ($deal->isDirty()) {
                $deal->save();
            }

            $this->recordStageHistory($deal, null, $deal->pipeline_stage_id, __('crm::deal.history.created'));
            AuditLogger::logCreated($deal);

            Log::info('CRM deal created', [
                'deal_id' => $deal->id,
                'title' => $deal->title,
                'actor_id' => auth()->id(),
            ]);
        }

        return $deal;
    }

    public function update(Deal $deal, DealData $data, array $services = []): ?Deal
    {
        $before = AuditLogger::auditableSnapshot($deal);
        $previousStageId = $deal->pipeline_stage_id;
        $updated = $this->repository->update($deal, $data);

        if ($updated) {
            $this->syncServices($deal, $services);
        }

        if ($updated && $previousStageId !== $deal->pipeline_stage_id) {
            $fromStage = $this->stageRepository->findOrFail($previousStageId);
            $toStage = $this->stageRepository->findOrFail($deal->pipeline_stage_id);
            $this->applyStageOutcome($deal, $toStage);
            $deal->save();
            $this->recordStageHistory($deal, $previousStageId, $deal->pipeline_stage_id);
            AuditLogger::logStageChanged($deal, $fromStage->name, $toStage->name);
            $this->dispatchStageChanged($deal, $fromStage, $toStage);
        } elseif ($updated) {
            AuditLogger::logUpdated($deal, $before, AuditLogger::auditableSnapshot($deal->fresh()));
        }

        if ($updated) {
            Log::info('CRM deal updated', [
                'deal_id' => $deal->id,
                'title' => $deal->title,
                'actor_id' => auth()->id(),
            ]);
        }

        return $updated;
    }

    public function moveStage(Deal $deal, int $stageId, ?string $notes = null): Deal
    {
        return DB::transaction(function () use ($deal, $stageId, $notes) {
            $previousStageId = $deal->pipeline_stage_id;
            $stage = $this->stageRepository->findOrFail($stageId);

            if ($previousStageId === $stage->id) {
                return $deal;
            }

            $deal->pipeline_stage_id = $stage->id;
            $this->applyStageOutcome($deal, $stage);
            $deal->save();

            $this->recordStageHistory($deal, $previousStageId, $stage->id, $notes);
            AuditLogger::logStageChanged($deal, $previousStageId ? $this->stageRepository->findOrFail($previousStageId)->name : '—', $stage->name);
            $this->dispatchStageChanged(
                $deal,
                $previousStageId ? $this->stageRepository->findOrFail($previousStageId) : null,
                $stage
            );

            Log::info('CRM deal stage moved', [
                'deal_id' => $deal->id,
                'from_stage_id' => $previousStageId,
                'to_stage_id' => $stage->id,
                'actor_id' => auth()->id(),
            ]);

            session()->flushMessage(true);

            return $deal->fresh(['company', 'pipelineStage', 'assignee']);
        });
    }

    public function delete(Deal $deal): ?bool
    {
        AuditLogger::logDeleted($deal);
        $deleted = $this->repository->delete($deal);

        if ($deleted) {
            Log::warning('CRM deal deleted', [
                'deal_id' => $deal->id,
                'title' => $deal->title,
                'actor_id' => auth()->id(),
            ]);
        }

        return $deleted;
    }

    public function bulkDelete(array $ids): ?bool
    {
        $deleted = $this->repository->bulkDelete($ids);

        if ($deleted) {
            Log::warning('CRM deals bulk deleted', [
                'deal_ids' => $ids,
                'actor_id' => auth()->id(),
            ]);
        }

        return $deleted;
    }

    private function applyStageOutcome(Deal $deal, PipelineStage $stage): void
    {
        if ($stage->is_won) {
            $deal->status = Deal::STATUS_WON;
            $deal->won_at = now();
            $deal->lost_at = null;
            $deal->lost_reason = null;
            $deal->closed_at = now();
            $deal->probability = $stage->probability ?: 100;

            return;
        }

        if ($stage->is_lost) {
            $deal->status = Deal::STATUS_LOST;
            $deal->lost_at = now();
            $deal->won_at = null;
            $deal->closed_at = now();
            $deal->probability = $stage->probability ?: 0;

            return;
        }

        $deal->status = Deal::STATUS_OPEN;
        $deal->won_at = null;
        $deal->lost_at = null;
        $deal->closed_at = null;
        $deal->lost_reason = null;

        if ($stage->probability > 0) {
            $deal->probability = $stage->probability;
        }
    }

    private function recordStageHistory(Deal $deal, ?int $fromStageId, int $toStageId, ?string $notes = null): void
    {
        DealStageHistory::create([
            'deal_id' => $deal->id,
            'from_pipeline_stage_id' => $fromStageId,
            'to_pipeline_stage_id' => $toStageId,
            'changed_by' => auth()->id(),
            'notes' => $notes,
        ]);
    }

    private function dispatchStageChanged(Deal $deal, ?PipelineStage $fromStage, PipelineStage $toStage): void
    {
        DealStageChanged::dispatch($deal, $fromStage, $toStage, auth()->user());
    }

    public function syncServices(Deal $deal, array $services): void
    {
        $sync = [];

        foreach ($services as $row) {
            if (empty($row['service_id'])) {
                continue;
            }

            $sync[(int) $row['service_id']] = [
                'quantity' => max(1, (int) ($row['quantity'] ?? 1)),
                'unit_price' => (float) ($row['unit_price'] ?? 0),
            ];
        }

        $deal->services()->sync($sync);
    }
}
