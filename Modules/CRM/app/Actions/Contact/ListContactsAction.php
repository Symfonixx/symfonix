<?php

namespace Modules\CRM\Actions\Contact;

use Modules\CRM\Services\Contact\ContactService;

class ListContactsAction
{
    public function __construct(private readonly ContactService $service) {}

    public function execute(array $filters = [])
    {
        return $this->service->list($filters);
    }
}
