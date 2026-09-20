<?php

namespace Modules\AI\Services\Assistant;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\AI\Models\AiConversation;
use Modules\AI\Models\AiMessage;
use Modules\AI\Support\CostLimiter;

class ConversationService
{
    public function __construct(private readonly CostLimiter $limiter) {}

    /**
     * @return Collection<int, AiConversation>
     */
    public function listFor(User $user): Collection
    {
        return AiConversation::query()
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->limit(30)
            ->get(['id', 'title', 'updated_at', 'created_at']);
    }

    public function create(User $user, ?string $title = null): AiConversation
    {
        return AiConversation::query()->create([
            'user_id' => $user->id,
            'title' => $title,
        ]);
    }

    public function findOwned(User $user, int $id): ?AiConversation
    {
        return AiConversation::query()
            ->where('user_id', $user->id)
            ->whereKey($id)
            ->first();
    }

    public function deleteOwned(User $user, AiConversation $conversation): void
    {
        abort_unless($conversation->belongsToUser($user), 404);
        $conversation->delete();
    }

    public function appendUserMessage(AiConversation $conversation, string $content): AiMessage
    {
        if (blank($conversation->title)) {
            $conversation->forceFill([
                'title' => Str::limit(trim($content), 60, ''),
            ])->save();
        }

        $message = $conversation->messages()->create([
            'role' => AiMessage::ROLE_USER,
            'content' => $content,
            'metadata' => null,
        ]);

        $conversation->touch();

        return $message;
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function appendAssistantMessage(AiConversation $conversation, string $content, array $metadata = []): AiMessage
    {
        $message = $conversation->messages()->create([
            'role' => AiMessage::ROLE_ASSISTANT,
            'content' => $content,
            'metadata' => $metadata === [] ? null : $metadata,
        ]);

        $conversation->touch();

        return $message;
    }

    /**
     * @param  list<array{id: string, name: string, arguments: array<string, mixed>}>  $toolCalls
     */
    public function appendAssistantToolCall(AiConversation $conversation, array $toolCalls, ?string $content = null): AiMessage
    {
        return $conversation->messages()->create([
            'role' => AiMessage::ROLE_ASSISTANT,
            'content' => $content,
            'metadata' => [
                'tool_calls' => $toolCalls,
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function appendToolMessage(AiConversation $conversation, string $content, array $metadata): AiMessage
    {
        return $conversation->messages()->create([
            'role' => AiMessage::ROLE_TOOL,
            'content' => $this->limiter->truncateString($content) ?? $content,
            'metadata' => $metadata,
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function historyForModel(AiConversation $conversation): array
    {
        $messages = $conversation->messages()
            ->whereIn('role', [AiMessage::ROLE_USER, AiMessage::ROLE_ASSISTANT, AiMessage::ROLE_TOOL])
            ->orderBy('id')
            ->get();

        $mapped = [];

        foreach ($messages as $message) {
            $row = [
                'role' => $message->role,
                'content' => $message->content ?? '',
            ];

            if ($message->role === AiMessage::ROLE_ASSISTANT && ! empty($message->metadata['tool_calls'])) {
                $row['tool_calls'] = $message->metadata['tool_calls'];
            }

            if ($message->role === AiMessage::ROLE_TOOL) {
                $row['tool_call_id'] = $message->metadata['tool_call_id'] ?? '';
                $row['name'] = $message->metadata['name'] ?? '';
            }

            $mapped[] = $row;
        }

        return $this->limiter->trimMessages($mapped);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function visibleMessages(AiConversation $conversation): array
    {
        return $conversation->messages()
            ->whereIn('role', [AiMessage::ROLE_USER, AiMessage::ROLE_ASSISTANT])
            ->orderBy('id')
            ->get()
            ->filter(function (AiMessage $message) {
                return empty($message->metadata['tool_calls'] ?? null);
            })
            ->map(fn (AiMessage $message) => $this->serializeVisible($message))
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function serializeVisible(AiMessage $message): array
    {
        $metadata = $message->metadata ?? [];

        return [
            'id' => $message->id,
            'role' => $message->role,
            'content' => $message->content,
            'sources' => $metadata['sources'] ?? [],
            'created_at' => $message->created_at?->toIso8601String(),
            'can_regenerate' => $message->role === AiMessage::ROLE_ASSISTANT,
        ];
    }

    public function removeAfterLastUserMessage(AiConversation $conversation): ?AiMessage
    {
        $lastUser = $conversation->messages()
            ->where('role', AiMessage::ROLE_USER)
            ->orderByDesc('id')
            ->first();

        if ($lastUser === null) {
            return null;
        }

        $conversation->messages()
            ->where('id', '>', $lastUser->id)
            ->delete();

        return $lastUser;
    }
}
