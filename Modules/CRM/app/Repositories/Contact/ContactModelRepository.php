<?php

namespace Modules\CRM\Repositories\Contact;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\CRM\DTOs\Contact\ContactData;
use Modules\CRM\Models\Contact;

class ContactModelRepository implements ContactRepository
{
    use ExceptionHandlerTrait;

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Contact::query()
            ->with('company:id,name')
            ->filter($filters)
            ->latest()
            ->paginate($perPage);
    }

    public function findOrFail(int $id, bool $withTrashed = false): Contact
    {
        $query = Contact::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->findOrFail($id);
    }

    public function create(ContactData $data): ?Contact
    {
        return $this->execute(function () use ($data) {
            $contact = Contact::create($data->toArray());
            session()->flushMessage(true);

            return $contact;
        });
    }

    public function update(Contact $contact, ContactData $data): ?Contact
    {
        return $this->execute(function () use ($contact, $data) {
            $contact->update($data->toArray());
            session()->flushMessage(true);

            return $contact;
        });
    }

    public function delete(Contact $contact): ?bool
    {
        return $this->execute(function () use ($contact) {
            $deleted = $contact->delete();
            session()->flushMessage(true);

            return $deleted;
        });
    }

    public function bulkDelete(array $ids): ?bool
    {
        return $this->execute(function () use ($ids) {
            $deleted = Contact::whereIn('id', $ids)->delete();
            session()->flushMessage(true);

            return (bool) $deleted;
        });
    }
}
