<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Modules\AI\Support\CostLimiter;
use Modules\AI\Support\QueryableEntityRegistry;
use Modules\AI\Support\ToolResult;

class QueryEntityTool extends AbstractAssistantTool
{
    public function __construct(
        CostLimiter $limiter,
        private readonly QueryableEntityRegistry $registry,
    ) {
        parent::__construct($limiter);
    }

    public function name(): string
    {
        return 'query_entity';
    }

    public function description(): string
    {
        return 'Search, list, or count rows from an allowlisted business entity (leads, contacts, customers, deals, quotes, invoices, projects, campaigns, employees, tickets, products, services, tax, CMS, and more). Prefer specialized stats tools when aggregating revenue/KPIs. Use list_queryable_entities if you need valid entity keys.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'entity' => [
                    'type' => 'string',
                    'enum' => $this->registry->keys(),
                    'description' => 'Entity key from list_queryable_entities',
                ],
                'action' => [
                    'type' => 'string',
                    'enum' => ['search', 'list', 'count'],
                    'description' => 'search=match query text; list=recent rows with optional filters; count=total matching rows',
                ],
                'query' => [
                    'type' => 'string',
                    'description' => 'Search text (required for action=search)',
                ],
                'filters' => [
                    'type' => 'object',
                    'description' => 'Exact match filters using only columns listed for that entity (e.g. status, company_id)',
                    'additionalProperties' => true,
                ],
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Max rows to return for search/list (capped by system limits)',
                ],
            ],
            'required' => ['entity', 'action'],
        ];
    }

    public function permissions(): array
    {
        // Authorization is per-entity inside execute().
        return [];
    }

    public function authorized(User $user): bool
    {
        return $this->registry->authorizedCatalog($user) !== [];
    }

    protected function execute(User $user, array $arguments): ToolResult
    {
        $entity = (string) ($arguments['entity'] ?? '');
        $action = (string) ($arguments['action'] ?? 'list');
        $definition = $this->registry->get($entity);

        if ($definition === null) {
            return ToolResult::empty(__('ai::assistant.errors.not_found'), ['Queryable entities']);
        }

        if (! $this->registry->authorized($user, $entity)) {
            return ToolResult::denied($this->deniedMessage());
        }

        /** @var class-string<Model> $modelClass */
        $modelClass = $definition['model'];
        $query = $modelClass::query();

        $filters = is_array($arguments['filters'] ?? null) ? $arguments['filters'] : [];
        $this->registry->applyFilters($query, $definition, $filters);

        if ($action === 'search') {
            $search = trim((string) ($arguments['query'] ?? ''));
            if ($search === '') {
                return ToolResult::empty(__('ai::assistant.errors.not_found'), [$definition['label']]);
            }
            if ($definition['searchable'] === []) {
                return ToolResult::empty('This entity does not support text search. Use action=list with filters instead.', [$definition['label']]);
            }
            $this->registry->applySearch($query, $definition, $search);
        } elseif ($action !== 'list' && $action !== 'count') {
            return ToolResult::empty(__('ai::assistant.errors.not_found'), [$definition['label']]);
        }

        if ($action === 'count') {
            return ToolResult::success([
                'entity' => $entity,
                'action' => 'count',
                'count' => (int) $query->count(),
                'filters' => $filters,
            ], [$definition['label']]);
        }

        $limit = isset($arguments['limit']) ? (int) $arguments['limit'] : $this->limiter->maxListItems();
        $limit = max(1, min($limit, $this->limiter->maxListItems()));

        $rows = $query
            ->orderByDesc($definition['order_by'])
            ->limit($limit)
            ->get($definition['columns']);

        $results = $rows->map(
            fn (Model $model) => $this->registry->serializeModel($model, $definition, $this->limiter)
        )->all();

        return ToolResult::success([
            'entity' => $entity,
            'action' => $action,
            'count' => count($results),
            'results' => $results,
        ], [$definition['label']]);
    }
}
