<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Modules\AI\Support\CostLimiter;
use Modules\AI\Support\QueryableEntityRegistry;
use Modules\AI\Support\ToolResult;

class ListQueryableEntitiesTool extends AbstractAssistantTool
{
    public function __construct(
        CostLimiter $limiter,
        private readonly QueryableEntityRegistry $registry,
    ) {
        parent::__construct($limiter);
    }

    public function name(): string
    {
        return 'list_queryable_entities';
    }

    public function description(): string
    {
        return 'List every business data entity you are allowed to query (leads, contacts, invoices, projects, campaigns, employees, etc.). Call this first when unsure which entity key to use with query_entity or get_entity_record.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => (object) [],
        ];
    }

    public function permissions(): array
    {
        return [];
    }

    protected function execute(User $user, array $arguments): ToolResult
    {
        $entities = $this->registry->authorizedCatalog($user);

        if ($entities === []) {
            return ToolResult::empty(__('ai::assistant.errors.permission'), ['Queryable entities']);
        }

        return ToolResult::success([
            'entities' => $entities,
            'hint' => 'Use query_entity to search/list/count, or get_entity_record for one row by id.',
        ], ['Queryable entities']);
    }
}
