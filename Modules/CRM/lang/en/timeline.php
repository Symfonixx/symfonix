<?php

return [
    'title' => 'Activity Timeline',
    'add_activity' => 'Log Activity',
    'no_entries' => 'No activities or audit entries yet.',
    'by' => 'by',
    'system' => 'System',
    'fields_changed' => 'Fields changed',
    'delete_activity' => 'Remove activity',
    'activity_types' => [
        'note' => 'Note',
        'call' => 'Call',
        'meeting' => 'Meeting',
        'task' => 'Task',
        'email' => 'Email',
    ],
    'fields' => [
        'type' => 'Type',
        'title' => 'Title',
        'body' => 'Details',
        'scheduled_at' => 'Scheduled At',
        'completed_at' => 'Completed At',
    ],
    'placeholders' => [
        'title' => 'Optional short title',
        'body' => 'What happened? Add context for your team...',
    ],
    'audit' => [
        'created' => ':name was created',
        'updated' => ':name was updated',
        'deleted' => ':name was deleted',
        'stage_changed' => 'Stage changed from :from to :to',
        'activity_logged' => 'Activity logged: :type',
        'activity_removed' => 'Activity removed: :type',
        'converted' => 'Converted to deal',
    ],
    'events' => [
        'created' => 'Created',
        'updated' => 'Updated',
        'deleted' => 'Deleted',
        'stage_changed' => 'Stage Changed',
        'activity_logged' => 'Activity Logged',
        'activity_removed' => 'Activity Removed',
        'converted' => 'Converted',
        'restored' => 'Restored',
        'activity_logged' => 'Activity Logged',
        'activity_removed' => 'Activity Removed',
    ],
    'errors' => [
        'invalid_subject' => 'Invalid CRM subject type.',
    ],
];
