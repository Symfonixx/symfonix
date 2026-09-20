<?php

namespace App\BotMan;

use BotMan\Drivers\Web\WebDriver;

class CapturingWebDriver extends WebDriver
{
    /**
     * @var array{status: int, messages: list<array<string, mixed>>}
     */
    public static array $lastPayload = [
        'status' => 200,
        'messages' => [],
    ];

    public function messagesHandled()
    {
        $messages = $this->buildReply($this->replies);
        $this->replies = [];

        self::$lastPayload = [
            'status' => $this->replyStatusCode,
            'messages' => array_values($messages),
        ];
    }
}
