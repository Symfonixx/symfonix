<?php

namespace Modules\CRM\Actions\Contact;

use Modules\CRM\Models\ContactForm;
use Modules\CRM\Models\Lead;
use Modules\CRM\Services\Contact\ContactFormConversionService;

class ConvertContactFormToLeadAction
{
    public function __construct(private readonly ContactFormConversionService $service) {}

    public function execute(ContactForm $form): Lead
    {
        return $this->service->convertToLead($form);
    }
}
