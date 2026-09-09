<?php

namespace Modules\CRM\Services\Lead;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Modules\CRM\DTOs\Lead\LeadData;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\LeadCustomField;
use Modules\CRM\Repositories\Lead\LeadRepository;
use Modules\CRM\Support\AuditLogger;

class LeadService
{
    public function __construct(private readonly LeadRepository $repository) {}

    public function list(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, (int) config('core.page_size', 15));
    }

    public function storeAttachments(Lead $lead, array $files): void
    {
        if ($files === []) {
            return;
        }

        $stored = $lead->attachments ?? [];

        foreach ($files as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $path = $file->store('leads/attachments', 'public');
            $stored[] = [
                'path' => $path,
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            ];
        }

        if ($stored !== ($lead->attachments ?? [])) {
            $lead->update(['attachments' => $stored]);
        }
    }

    public function syncServices(Lead $lead, array $serviceIds): void
    {
        $ids = collect($serviceIds)
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $lead->services()->sync($ids);
        $lead->update(['service_id' => $ids[0] ?? null]);
    }

    public function syncTags(Lead $lead, array $tagIds): void
    {
        $ids = collect($tagIds)
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $lead->tags()->sync($ids);
    }

    public function syncCustomFields(Lead $lead, array $values): void
    {
        $fields = LeadCustomField::query()->active()->ordered()->get();
        $existing = $lead->custom_fields ?? [];

        foreach ($fields as $field) {
            $raw = $values[$field->key] ?? null;

            if ($field->type === LeadCustomField::TYPE_CHECKBOX) {
                $existing[$field->key] = filter_var($raw, FILTER_VALIDATE_BOOLEAN);
                continue;
            }

            if ($raw === null || $raw === '') {
                unset($existing[$field->key]);
                continue;
            }

            $existing[$field->key] = is_scalar($raw) ? (string) $raw : $raw;
        }

        $lead->update(['custom_fields' => $existing === [] ? null : $existing]);
    }

    public function create(LeadData $data): ?Lead
    {
        $lead = $this->repository->create($data);

        if ($lead) {
            AuditLogger::logCreated($lead);

            Log::info('CRM lead created', [
                'lead_id' => $lead->id,
                'source' => $lead->source,
                'actor_id' => auth()->id(),
            ]);
        }

        return $lead;
    }

    public function update(Lead $lead, LeadData $data): ?Lead
    {
        $before = AuditLogger::auditableSnapshot($lead);
        $updated = $this->repository->update($lead, $data);

        if ($updated) {
            AuditLogger::logUpdated($lead, $before, AuditLogger::auditableSnapshot($lead->fresh()));

            Log::info('CRM lead updated', [
                'lead_id' => $lead->id,
                'actor_id' => auth()->id(),
            ]);
        }

        return $updated;
    }

    public function delete(Lead $lead): ?bool
    {
        AuditLogger::logDeleted($lead);
        $deleted = $this->repository->delete($lead);

        if ($deleted) {
            Log::warning('CRM lead deleted', [
                'lead_id' => $lead->id,
                'actor_id' => auth()->id(),
            ]);
        }

        return $deleted;
    }

    public function bulkDelete(array $ids): ?bool
    {
        $deleted = $this->repository->bulkDelete($ids);

        if ($deleted) {
            Log::warning('CRM leads bulk deleted', [
                'lead_ids' => $ids,
                'actor_id' => auth()->id(),
            ]);
        }

        return $deleted;
    }

    public function block(Lead $lead): ?Lead
    {
        return $this->setBlocked($lead, true);
    }

    public function unblock(Lead $lead): ?Lead
    {
        return $this->setBlocked($lead, false);
    }

    private function setBlocked(Lead $lead, bool $blocked): ?Lead
    {
        $updated = $this->repository->setBlocked($lead, $blocked);

        if ($updated) {
            Log::info('CRM lead block status changed', [
                'lead_id' => $lead->id,
                'blocked' => $blocked,
                'actor_id' => auth()->id(),
            ]);
        }

        return $updated;
    }
}
