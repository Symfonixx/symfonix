<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Support\Models\ContactForm;

class NewContactFormSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public ContactForm $contact;

    public function __construct(ContactForm $contact)
    {
        $this->contact = $contact;
    }

    public function build(): self
    {
        $subject = $this->contact->subject ?: 'New contact form';

        return $this->subject("Contact form: {$subject}")
            ->markdown('emails.admin.contact-form', [
                'contact' => $this->contact,
                'adminUrl' => route('admin.contact_forms.index'),
            ]);
    }
}
