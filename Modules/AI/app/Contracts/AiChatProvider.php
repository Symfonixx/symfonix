<?php

namespace Modules\AI\Contracts;

interface AiChatProvider
{
    public function isConfigured(): bool;

    /**
     * @param  list<array<string, mixed>>  $messages
     * @param  list<array{name: string, description: string, parameters: array<string, mixed>}>  $tools
     * @param  array{temperature?: float, max_tokens?: int, timeout?: int}  $options
     * @return array{success: bool, content: ?string, tool_calls: list<array{id: string, name: string, arguments: array<string, mixed>}>, error: ?string, provider: string}
     */
    public function chat(array $messages, array $tools = [], array $options = []): array;

    public function providerName(): string;
}
