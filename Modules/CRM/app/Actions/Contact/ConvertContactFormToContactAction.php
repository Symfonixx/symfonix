<?php

namespace Modules\CRM\Actions\Contact;

use Modules\CRM\Models\Contact;
use Modules\CRM\Models\ContactForm;
use Modules\CRM\Services\Contact\ContactFormConversionService;

class ConvertContactFormToContactAction
{
    public function __construct(private readonly ContactFormConversionService $service) {}

    public function execute(ContactForm $form): Contact
    {
        return $this->service->convertToContact($form);
    }
}
