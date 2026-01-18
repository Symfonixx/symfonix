<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class MainMenuConversation extends Conversation
{
    public function run(): void
    {
        $this->showMainMenu();
    }

    protected function showMainMenu(): void
    {
        $question = Question::create(__('chat.menu.title'))
            ->addButtons([
                Button::create(__('chat.menu.price_quote'))->value('price_quote'),
                Button::create(__('chat.menu.branch_complaint'))->value('branch_complaint'),
                Button::create(__('chat.menu.contact_branch'))->value('contact_branch'),
            ]);

        $this->ask($question, function (Answer $answer) {
            if (! $answer->isInteractiveMessageReply()) {
                // If the user types text instead of clicking, show the menu again.
                return $this->showMainMenu();
            }

            $value = $answer->getValue();

            if ($value === 'price_quote') {
                $this->bot->startConversation(new PriceQuoteConversation());
            } elseif ($value === 'branch_complaint') {
                $this->bot->startConversation(new BranchComplaintConversation());
            } elseif ($value === 'contact_branch') {
                $this->bot->startConversation(new ContactBranchConversation());
            } else {
                // Unknown option – simply re-show the menu.
                $this->showMainMenu();
            }
        });
    }
}


