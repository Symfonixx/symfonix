<?php

namespace Modules\CRM\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MarketingEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $emailSubject,
        public string $emailBody,
    ) {}

    public function build(): self
    {
        return $this->subject($this->emailSubject)
            ->markdown('crm::emails.marketing', [
                'subject' => $this->emailSubject,
                'body' => $this->emailBody,
            ]);
    }
}
