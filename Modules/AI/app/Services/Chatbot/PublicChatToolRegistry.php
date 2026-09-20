<?php

namespace Modules\AI\Services\Chatbot;

use Modules\AI\Contracts\PublicChatTool;
use Modules\AI\Support\CostLimiter;
use Modules\AI\Support\PublicChatContext;
use Modules\AI\Support\ToolResult;

class PublicChatToolRegistry
{
    /**
     * @param  list<PublicChatTool>  $tools
     */
    public function __construct(
        private readonly array $tools,
        private readonly CostLimiter $limiter,
    ) {}

    /**
     * @return list<array{name: string, description: string, parameters: array<string, mixed>}>
     */
    public function definitions(): array
    {
        $definitions = [];

        foreach ($this->tools as $tool) {
            $definitions[] = [
                'name' => $tool->name(),
                'description' => $tool->description(),
                'parameters' => $tool->parameters(),
            ];
        }

        return $definitions;
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function execute(string $name, array $arguments, PublicChatContext $context): ToolResult
    {
        foreach ($this->tools as $tool) {
            if ($tool->name() !== $name) {
                continue;
            }

            $result = $tool->handle($arguments, $context);
            $data = $this->limiter->truncate($result->data);

            return new ToolResult(
                $result->ok,
                is_array($data) ? $data : ['value' => $data],
                $result->sources,
                $result->denied,
                $result->error,
            );
        }

        return ToolResult::denied(__('chat.ai.unknown_tool'));
    }
}
