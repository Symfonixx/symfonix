<?php

namespace Modules\CRM\Services\Activity;

use Illuminate\Database\Eloquent\Model;
use Modules\CRM\DTOs\Activity\ActivityData;
use Modules\CRM\Models\CrmActivity;
use Modules\CRM\Support\AuditLogger;

class ActivityService
{
    public function create(Model $subject, ActivityData $data): CrmActivity
    {
        $activity = CrmActivity::create([
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => $subject->getKey(),
            'type' => $data->type,
            'title' => $data->title,
            'body' => $data->body,
            'scheduled_at' => $data->scheduled_at,
            'completed_at' => $data->completed_at,
            'user_id' => auth()->id(),
        ]);

        AuditLogger::log(
            $subject,
            'activity_logged',
            __('crm::timeline.audit.activity_logged', [
                'type' => __('crm::timeline.activity_types.'.$data->type),
            ]),
            null,
            [
                'activity_id' => $activity->id,
                'type' => $data->type,
                'title' => $data->title,
            ],
        );

        session()->flushMessage(true);

        return $activity->load('user:id,name');
    }

    public function delete(CrmActivity $activity): bool
    {
        $subject = $activity->subject;

        $deleted = (bool) $activity->delete();

        if ($deleted && $subject) {
            AuditLogger::log(
                $subject,
                'activity_removed',
                __('crm::timeline.audit.activity_removed', [
                    'type' => __('crm::timeline.activity_types.'.$activity->type),
                ]),
                ['activity_id' => $activity->id],
                null,
            );
        }

        session()->flushMessage(true);

        return $deleted;
    }
}
