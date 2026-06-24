<?php

namespace Modules\CRM\Support;

use Illuminate\Database\Eloquent\Model;
use Modules\CRM\Models\CrmAuditLog;

class AuditLogger
{
    public static function log(
        Model $subject,
        string $event,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): CrmAuditLog {
        return CrmAuditLog::create([
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => $subject->getKey(),
            'event' => $event,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => auth()->id(),
            'ip_address' => request()?->ip(),
        ]);
    }

    public static function logCreated(Model $subject, ?string $description = null): CrmAuditLog
    {
        return self::log(
            $subject,
            CrmAuditLog::EVENT_CREATED,
            $description ?? __('crm::timeline.audit.created', ['name' => self::subjectLabel($subject)]),
            null,
            self::auditableSnapshot($subject),
        );
    }

    public static function logUpdated(Model $subject, array $before, array $after, ?string $description = null): ?CrmAuditLog
    {
        $diff = self::diff($before, $after);

        if ($diff['old'] === [] && $diff['new'] === []) {
            return null;
        }

        return self::log(
            $subject,
            CrmAuditLog::EVENT_UPDATED,
            $description ?? __('crm::timeline.audit.updated', ['name' => self::subjectLabel($subject)]),
            $diff['old'],
            $diff['new'],
        );
    }

    public static function logDeleted(Model $subject, ?string $description = null): CrmAuditLog
    {
        return self::log(
            $subject,
            CrmAuditLog::EVENT_DELETED,
            $description ?? __('crm::timeline.audit.deleted', ['name' => self::subjectLabel($subject)]),
            self::auditableSnapshot($subject),
            null,
        );
    }

    public static function logStageChanged(
        Model $subject,
        string $fromLabel,
        string $toLabel,
        ?array $extra = null,
    ): CrmAuditLog {
        return self::log(
            $subject,
            CrmAuditLog::EVENT_STAGE_CHANGED,
            __('crm::timeline.audit.stage_changed', ['from' => $fromLabel, 'to' => $toLabel]),
            ['stage' => $fromLabel],
            array_merge(['stage' => $toLabel], $extra ?? []),
        );
    }

    public static function auditableSnapshot(Model $subject, array $only = []): array
    {
        $attributes = $only !== [] ? $only : array_keys($subject->getAttributes());
        $snapshot = [];

        foreach ($attributes as $key) {
            if (in_array($key, ['created_at', 'updated_at', 'deleted_at', 'password', 'remember_token'], true)) {
                continue;
            }

            $value = $subject->getAttribute($key);

            if ($value instanceof \DateTimeInterface) {
                $value = $value->format('Y-m-d H:i:s');
            }

            $snapshot[$key] = $value;
        }

        return $snapshot;
    }

    public static function diff(array $before, array $after): array
    {
        $old = [];
        $new = [];

        foreach ($after as $key => $value) {
            $previous = $before[$key] ?? null;

            if ($previous != $value) {
                $old[$key] = $previous;
                $new[$key] = $value;
            }
        }

        return ['old' => $old, 'new' => $new];
    }

    private static function subjectLabel(Model $subject): string
    {
        return (string) ($subject->name ?? $subject->title ?? class_basename($subject).' #'.$subject->getKey());
    }
}
