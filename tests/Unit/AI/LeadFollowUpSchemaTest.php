<?php

namespace Tests\Unit\AI;

use Modules\AI\Support\LeadFollowUpSchema;
use Modules\CRM\Models\CrmActivity;
use Tests\TestCase;

class LeadFollowUpSchemaTest extends TestCase
{
    public function test_it_normalizes_type_aliases_and_unknown_values(): void
    {
        $this->assertSame(CrmActivity::TYPE_TASK, LeadFollowUpSchema::normalizeType('follow-up'));
        $this->assertSame(CrmActivity::TYPE_CALL, LeadFollowUpSchema::normalizeType('phone'));
        $this->assertSame(CrmActivity::TYPE_EMAIL, LeadFollowUpSchema::normalizeType('mail'));
        $this->assertSame(CrmActivity::TYPE_TASK, LeadFollowUpSchema::normalizeType('unknown'));
        $this->assertSame(CrmActivity::TYPE_MEETING, LeadFollowUpSchema::normalizeType('meeting'));
    }

    public function test_it_normalizes_future_scheduled_at_for_datetime_local(): void
    {
        $scheduled = now()->addDays(2)->setTime(14, 30);
        $normalized = LeadFollowUpSchema::normalizeScheduledAt($scheduled->toIso8601String());

        $this->assertSame($scheduled->format('Y-m-d\TH:i'), $normalized);
    }

    public function test_it_replaces_past_or_invalid_dates_with_tomorrow_morning(): void
    {
        $fallback = now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i');

        $this->assertSame($fallback, LeadFollowUpSchema::normalizeScheduledAt('not-a-date'));
        $this->assertSame($fallback, LeadFollowUpSchema::normalizeScheduledAt(now()->subDay()->toDateTimeString()));
    }

    public function test_it_normalizes_a_full_payload(): void
    {
        $fields = LeadFollowUpSchema::normalize([
            'type' => 'EMAIL',
            'title' => '  Check the website quote  ',
            'body' => 'Please confirm the homepage scope.',
            'scheduled_at' => now()->addDays(3)->setTime(9, 0)->format('Y-m-d H:i'),
        ]);

        $this->assertSame(CrmActivity::TYPE_EMAIL, $fields['type']);
        $this->assertSame('Check the website quote', $fields['title']);
        $this->assertSame('Please confirm the homepage scope.', $fields['body']);
        $this->assertSame(now()->addDays(3)->setTime(9, 0)->format('Y-m-d\TH:i'), $fields['scheduled_at']);
    }
}
