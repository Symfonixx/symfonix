<?php

namespace Modules\CRM\Actions\Contact;

use Modules\CRM\DTOs\Contact\ContactData;
use Modules\CRM\Models\Contact;
use Modules\CRM\Services\Contact\ContactService;

class CreateContactAction
{
    public function __construct(private readonly ContactService $service) {}

    public function execute(ContactData $data): ?Contact
    {
        return $this->service->create($data);
    }
}
