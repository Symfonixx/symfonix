<?php

namespace Modules\AI\Services\Assistant;

use App\Models\User;
use Modules\AI\Contracts\AssistantTool;
use Modules\AI\Support\CostLimiter;
use Modules\AI\Support\ToolResult;

class AssistantToolRegistry
{
    /**
     * @param  list<AssistantTool>  $tools
     */
    public function __construct(
        private readonly array $tools,
        private readonly CostLimiter $limiter,
    ) {}

    /**
     * @return list<array{name: string, description: string, parameters: array<string, mixed>}>
     */
    public function definitionsFor(User $user): array
    {
        $definitions = [];

        foreach ($this->tools as $tool) {
            if (! $tool->authorized($user)) {
                continue;
            }

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
    public function execute(User $user, string $name, array $arguments = []): ToolResult
    {
        foreach ($this->tools as $tool) {
            if ($tool->name() !== $name) {
                continue;
            }

            $result = $tool->handle($user, $arguments);
            $data = $this->limiter->truncate($result->data);

            return new ToolResult(
                $result->ok,
                is_array($data) ? $data : ['value' => $data],
                $result->sources,
                $result->denied,
                $result->error,
            );
        }

        return ToolResult::denied(__('ai::assistant.errors.unknown_tool'));
    }
}
