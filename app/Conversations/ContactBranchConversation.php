<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Modules\Base\Models\Branch;

class ContactBranchConversation extends Conversation
{
    public function run(): void
    {
        $this->showBranches();
    }

    protected function showBranches(): void
    {
        $branches = Branch::all();

        if ($branches->isEmpty()) {
            $this->say(__('chat.branches.none'));
            return;
        }

        $this->say(__('chat.contact.ask_branch'));

        foreach ($branches as $branch) {
            $phone = preg_replace('/\D+/', '', (string) $branch->phone);
            $url   = 'https://wa.me/' . $phone;

            // Send each branch as a direct WhatsApp link so clicking opens WhatsApp,
            // not another chat message.
            $this->say('<a href="' . e($url) . '" target="_blank">' . e($branch->name) . '</a>');
        }

        $this->say(__('chat.contact.hint'));
    }
}


