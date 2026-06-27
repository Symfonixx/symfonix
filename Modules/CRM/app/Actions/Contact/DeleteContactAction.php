<?php

namespace Modules\CRM\Actions\Contact;

use Modules\CRM\Models\Contact;
use Modules\CRM\Services\Contact\ContactService;

class DeleteContactAction
{
    public function __construct(private readonly ContactService $service) {}

    public function execute(Contact $contact): ?bool
    {
        return $this->service->delete($contact);
    }
}
