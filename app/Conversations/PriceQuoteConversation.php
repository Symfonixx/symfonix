<?php

namespace App\Conversations;

use Modules\Support\Models\ContactForm;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;

class PriceQuoteConversation extends Conversation
{
    protected string $name;
    protected string $email;
    protected string $phone;
    protected string $message;

    public function run(): void
    {
        $this->askName();
    }

    protected function askName(): void
    {
        $this->ask(__('chat.quote.ask_name'), function (Answer $answer) {
            $this->name = trim($answer->getText());

            $this->askEmail();
        });
    }

    protected function askEmail(): void
    {
        $this->ask(__('chat.quote.ask_email'), function (Answer $answer) {
            $email = trim($answer->getText());

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->say(__('chat.quote.email_invalid'));

                return $this->askEmail();
            }

            $this->email = $email;

            $this->askPhone();
        });
    }

    protected function askPhone(): void
    {
        $this->ask(__('chat.quote.ask_phone'), function (Answer $answer) {
            $phone = trim($answer->getText());

            if (! $this->isValidPhone($phone)) {
                $this->say(__('chat.quote.phone_invalid'));

                return $this->askPhone();
            }

            $this->phone = $phone;

            $this->askMessage();
        });
    }

    protected function askMessage(): void
    {
        $this->ask(__('chat.quote.ask_message'), function (Answer $answer) {
            $this->message = trim($answer->getText());

            $this->storeContactForm();
        });
    }

    protected function storeContactForm(): void
    {
        $subject = 'Get a Price Quote';

        ContactForm::create([
            'ip_address' => request()->ip(),
            'name'       => $this->name,
            'email'      => $this->email,
            'mobile'     => $this->phone,
            'subject'    => $subject,
            'message'    => $this->message,
            'blocked'    => false,
        ]);

        $this->say(__('chat.quote.success'));
    }

    /**
     * Basic phone/mobile validation: at least 8 digits.
     */
    protected function isValidPhone(string $value): bool
    {
        $digits = preg_replace('/\D+/', '', $value);

        return strlen($digits) >= 8;
    }
}


