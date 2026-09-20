<?php

namespace Tests\Unit\AI;

use Modules\AI\Support\CostLimiter;
use Tests\TestCase;

class AssistantCostLimiterTest extends TestCase
{
    public function test_it_truncates_long_strings(): void
    {
        config(['ai.assistant.max_record_chars' => 200]);

        $limiter = new CostLimiter;
        $value = str_repeat('a', 250);
        $truncated = (string) $limiter->truncateString($value);

        $this->assertSame(201, mb_strlen($truncated));
        $this->assertTrue(str_ends_with($truncated, '…'));
        $this->assertSame('short', $limiter->truncateString('short'));
    }

    public function test_it_limits_list_size(): void
    {
        config(['ai.assistant.max_list_items' => 3]);

        $limiter = new CostLimiter;

        $this->assertSame([1, 2, 3], $limiter->limitList([1, 2, 3, 4, 5]));
    }

    public function test_it_trims_history_but_keeps_system_message(): void
    {
        config(['ai.assistant.max_history_messages' => 4]);

        $limiter = new CostLimiter;
        $trimmed = $limiter->trimMessages([
            ['role' => 'system', 'content' => 'rules'],
            ['role' => 'user', 'content' => '1'],
            ['role' => 'assistant', 'content' => '2'],
            ['role' => 'user', 'content' => '3'],
            ['role' => 'assistant', 'content' => '4'],
            ['role' => 'user', 'content' => '5'],
        ]);

        $this->assertSame('rules', $trimmed[0]['content']);
        $this->assertCount(4, $trimmed);
        $this->assertSame('5', $trimmed[3]['content']);
    }
}
