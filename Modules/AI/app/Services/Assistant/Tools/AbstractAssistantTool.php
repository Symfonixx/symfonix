<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Carbon\Carbon;
use Modules\AI\Contracts\AssistantTool;
use Modules\AI\Support\AssistantPeriod;
use Modules\AI\Support\CostLimiter;
use Modules\AI\Support\ToolResult;

abstract class AbstractAssistantTool implements AssistantTool
{
    public function __construct(protected readonly CostLimiter $limiter) {}

    /**
     * @return list<string>
     */
    abstract public function permissions(): array;

    /**
     * @param  array<string, mixed>  $arguments
     */
    abstract protected function execute(User $user, array $arguments): ToolResult;

    public function authorized(User $user): bool
    {
        $permissions = $this->permissions();
        if ($permissions === []) {
            return true;
        }

        return $user->canany($permissions);
    }

    public function handle(User $user, array $arguments): ToolResult
    {
        if (! $this->authorized($user)) {
            return ToolResult::denied(__('ai::assistant.errors.permission'));
        }

        return $this->execute($user, $arguments);
    }

    /**
     * @return array<string, mixed>
     */
    protected function periodParameters(bool $required = false): array
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'period' => AssistantPeriod::schemaProperty(),
            ],
        ];

        if ($required) {
            $schema['required'] = ['period'];
        }

        return $schema;
    }

    /**
     * @param  array<string, mixed>  $arguments
     * @return array{period: string, label: string, start: Carbon, end: Carbon, previous_start: Carbon, previous_end: Carbon, month_keys: list<string>, source_label: string}
     */
    protected function period(array $arguments): array
    {
        $period = isset($arguments['period']) && is_string($arguments['period'])
            ? $arguments['period']
            : null;

        return AssistantPeriod::resolve($period);
    }

    protected function deniedMessage(): string
    {
        return __('ai::assistant.errors.permission');
    }

    protected function percentChange(float $current, float $previous): ?float
    {
        if ($previous == 0.0) {
            return null;
        }

        return round((($current - $previous) / abs($previous)) * 100, 1);
    }

    protected function trendDirection(float $current, float $previous): string
    {
        if ($current > $previous) {
            return 'up';
        }

        if ($current < $previous) {
            return 'down';
        }

        return 'flat';
    }

    /**
     * @return array{current: float, previous: float, growth_rate_percent: ?float, trend: string}
     */
    protected function growthMetric(float $current, float $previous): array
    {
        return [
            'current' => round($current, 2),
            'previous' => round($previous, 2),
            'growth_rate_percent' => $this->percentChange($current, $previous),
            'trend' => $this->trendDirection($current, $previous),
        ];
    }
}
