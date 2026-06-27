<?php

namespace Modules\CRM\Actions\Contact;

use Modules\CRM\DTOs\Contact\ContactData;
use Modules\CRM\Models\Contact;
use Modules\CRM\Services\Contact\ContactService;

class UpdateContactAction
{
    public function __construct(private readonly ContactService $service) {}

    public function execute(Contact $contact, ContactData $data): ?Contact
    {
        return $this->service->update($contact, $data);
    }
}
