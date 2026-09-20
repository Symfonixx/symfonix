<?php

namespace Modules\AI\Support;

final class CostLimiter
{
    public function maxHistoryMessages(): int
    {
        return max(4, (int) config('ai.assistant.max_history_messages', 16));
    }

    public function maxToolRounds(): int
    {
        return max(1, (int) config('ai.assistant.max_tool_rounds', 3));
    }

    public function maxListItems(): int
    {
        return max(3, (int) config('ai.assistant.max_list_items', 12));
    }

    public function maxRecordChars(): int
    {
        return max(200, (int) config('ai.assistant.max_record_chars', 1200));
    }

    public function temperature(): float
    {
        return (float) config('ai.assistant.temperature', 0.3);
    }

    public function maxTokens(): int
    {
        return max(256, (int) config('ai.assistant.max_tokens', 1200));
    }

    public function timeout(): int
    {
        return max(15, (int) config('ai.assistant.timeout', 90));
    }

    /**
     * @param  list<mixed>  $items
     * @return list<mixed>
     */
    public function limitList(array $items): array
    {
        return array_slice(array_values($items), 0, $this->maxListItems());
    }

    public function truncateString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $limit = $this->maxRecordChars();
        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        return mb_substr($value, 0, $limit).'…';
    }

    public function truncate(mixed $value): mixed
    {
        if (is_string($value)) {
            return $this->truncateString($value);
        }

        if (is_array($value)) {
            $out = [];
            foreach ($value as $key => $item) {
                $out[$key] = $this->truncate($item);
            }

            return $out;
        }

        return $value;
    }

    /**
     * Keep the most recent messages, preserving a leading system message when present.
     *
     * @param  list<array<string, mixed>>  $messages
     * @return list<array<string, mixed>>
     */
    public function trimMessages(array $messages): array
    {
        $max = $this->maxHistoryMessages();
        if (count($messages) <= $max) {
            return $messages;
        }

        $system = [];
        $rest = $messages;
        if (($messages[0]['role'] ?? null) === 'system') {
            $system[] = $messages[0];
            $rest = array_slice($messages, 1);
            $max = max(1, $max - 1);
        }

        return array_merge($system, array_slice($rest, -$max));
    }
}
