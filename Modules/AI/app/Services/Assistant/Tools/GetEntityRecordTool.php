<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Modules\AI\Support\CostLimiter;
use Modules\AI\Support\QueryableEntityRegistry;
use Modules\AI\Support\ToolResult;

class GetEntityRecordTool extends AbstractAssistantTool
{
    public function __construct(
        CostLimiter $limiter,
        private readonly QueryableEntityRegistry $registry,
    ) {
        parent::__construct($limiter);
    }

    public function name(): string
    {
        return 'get_entity_record';
    }

    public function description(): string
    {
        return 'Fetch one allowlisted business record by entity key and id. Use query_entity or search_records first when you only have a name.';
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
                'id' => [
                    'type' => 'integer',
                    'description' => 'Record id',
                ],
            ],
            'required' => ['entity', 'id'],
        ];
    }

    public function permissions(): array
    {
        return [];
    }

    public function authorized(User $user): bool
    {
        return $this->registry->authorizedCatalog($user) !== [];
    }

    protected function execute(User $user, array $arguments): ToolResult
    {
        $entity = (string) ($arguments['entity'] ?? '');
        $id = (int) ($arguments['id'] ?? 0);
        $definition = $this->registry->get($entity);

        if ($definition === null || $id < 1) {
            return ToolResult::empty(__('ai::assistant.errors.not_found'), ['Queryable entities']);
        }

        if (! $this->registry->authorized($user, $entity)) {
            return ToolResult::denied($this->deniedMessage());
        }

        /** @var class-string<Model> $modelClass */
        $modelClass = $definition['model'];
        $model = $modelClass::query()->find($id, $definition['columns']);

        if ($model === null) {
            return ToolResult::empty(__('ai::assistant.errors.not_found'), [$definition['label']]);
        }

        return ToolResult::success([
            'entity' => $entity,
            'record' => $this->registry->serializeModel($model, $definition, $this->limiter),
        ], [$definition['label']]);
    }
}
