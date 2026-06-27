<?php

namespace Modules\CRM\Actions\Contact;

use Modules\CRM\Services\Contact\ContactService;

class BulkDeleteContactsAction
{
    public function __construct(private readonly ContactService $service) {}

    public function execute(array $ids): ?bool
    {
        return $this->service->bulkDelete($ids);
    }
}
