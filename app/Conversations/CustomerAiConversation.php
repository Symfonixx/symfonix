<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Modules\AI\Services\Chatbot\PublicChatService;
use Modules\AI\Support\PublicChatContext;

class CustomerAiConversation extends Conversation
{
    protected ?string $initialMessage;

    /**
     * @var list<array<string, mixed>>
     */
    protected array $history = [];

    /**
     * @var list<array{role: string, message: string, meta?: array<string, mixed>, at?: string}>
     */
    protected array $transcript = [];

    protected ?int $leadId = null;

    protected ?int $serviceId = null;

    protected ?string $problemStatement = null;

    public function __construct(?string $initialMessage = null)
    {
        $this->initialMessage = $initialMessage ? trim($initialMessage) : null;
    }

    public function run(): void
    {
        if ($this->initialMessage) {
            $this->respondTo($this->initialMessage);

            return;
        }

        $this->ask(__('chat.lead.widget_intro'), function (Answer $answer) {
            $this->respondTo($this->answerText($answer));
        });
    }

    protected function respondTo(string $text): void
    {
        $text = trim($text);
        if ($text === '') {
            $this->listenAgain(__('chat.ai.empty'));

            return;
        }

        $this->addTranscript('user', $text);

        $context = $this->context();
        $result = app(PublicChatService::class)->reply($text, $this->history, $context);

        $this->leadId = $result['lead_id'] ?? $context->leadId;
        $this->serviceId = $context->serviceId;
        $this->problemStatement = $context->problemStatement;

        $reply = trim((string) ($result['reply'] ?? ''));
        if ($reply === '') {
            $reply = __('chat.ai.empty');
        }

        $this->history[] = ['role' => 'user', 'content' => $text];
        $this->history[] = ['role' => 'assistant', 'content' => $reply];

        $max = max(4, (int) config('ai.chatbot.max_history_messages', 12));
        if (count($this->history) > $max) {
            $this->history = array_slice($this->history, -$max);
        }
        $this->addTranscript('bot', $reply, [
            'provider' => $result['provider'] ?? null,
            'lead_id' => $this->leadId,
        ]);

        $this->listenAgain($reply, $result['buttons'] ?? []);
    }

    /**
     * @param  list<array{label: string, value: string}>  $buttons
     */
    protected function listenAgain(string $reply, array $buttons = []): void
    {
        if ($buttons !== []) {
            $question = Question::create($reply);
            foreach ($buttons as $button) {
                $label = trim((string) ($button['label'] ?? ''));
                if ($label === '') {
                    continue;
                }

                $value = trim((string) ($button['value'] ?? $label));
                $question->addButton(Button::create($label)->value($value !== '' ? $value : $label));
            }

            $this->ask($question, function (Answer $answer) {
                $this->respondTo($this->answerText($answer));
            });

            return;
        }

        $this->ask($reply, function (Answer $answer) {
            $this->respondTo($this->answerText($answer));
        });
    }

    protected function answerText(Answer $answer): string
    {
        if ($answer->isInteractiveMessageReply()) {
            $value = trim((string) $answer->getValue());
            if ($value !== '') {
                return $value;
            }
        }

        return trim($answer->getText());
    }

    protected function context(): PublicChatContext
    {
        return new PublicChatContext(
            botmanUserId: optional($this->bot->getUser())->getId(),
            botmanDriver: optional($this->bot->getDriver())->getName(),
            locale: app()->getLocale(),
            ipAddress: request()->ip(),
            transcript: $this->transcript,
            leadId: $this->leadId,
            serviceId: $this->serviceId,
            problemStatement: $this->problemStatement,
        );
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    protected function addTranscript(string $role, string $message, array $meta = []): void
    {
        $this->transcript[] = [
            'role' => $role,
            'message' => $message,
            'meta' => $meta,
            'at' => now()->toIso8601String(),
        ];
    }
}
