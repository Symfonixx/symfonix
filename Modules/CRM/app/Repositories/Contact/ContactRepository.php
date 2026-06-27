<?php

namespace Modules\CRM\Repositories\Contact;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\CRM\DTOs\Contact\ContactData;
use Modules\CRM\Models\Contact;

interface ContactRepository
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findOrFail(int $id, bool $withTrashed = false): Contact;

    public function create(ContactData $data): ?Contact;

    public function update(Contact $contact, ContactData $data): ?Contact;

    public function delete(Contact $contact): ?bool;

    public function bulkDelete(array $ids): ?bool;
}
