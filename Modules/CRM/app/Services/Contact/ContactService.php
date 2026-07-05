<?php

namespace Modules\CRM\Services\Contact;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Modules\CRM\DTOs\Contact\ContactData;
use Modules\CRM\Models\Contact;
use Modules\CRM\Models\Lead;
use Modules\CRM\Repositories\Contact\ContactRepository;
use Modules\CRM\Support\AuditLogger;

class ContactService
{
    public function __construct(private readonly ContactRepository $repository) {}

    public function list(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, (int) config('core.page_size', 15));
    }

    public function create(ContactData $data): ?Contact
    {
        $contact = $this->repository->create($data);

        if ($contact) {
            AuditLogger::logCreated($contact);

            Log::info('CRM contact created', [
                'contact_id' => $contact->id,
                'name' => $contact->name,
                'actor_id' => auth()->id(),
            ]);
        }

        return $contact;
    }

    public function update(Contact $contact, ContactData $data): ?Contact
    {
        $before = AuditLogger::auditableSnapshot($contact);
        $updated = $this->repository->update($contact, $data);

        if ($updated) {
            AuditLogger::logUpdated($contact, $before, AuditLogger::auditableSnapshot($contact->fresh()));

            Log::info('CRM contact updated', [
                'contact_id' => $contact->id,
                'name' => $contact->name,
                'actor_id' => auth()->id(),
            ]);
        }

        return $updated;
    }

    public function delete(Contact $contact): ?bool
    {
        AuditLogger::logDeleted($contact);
        $deleted = $this->repository->delete($contact);

        if ($deleted) {
            Log::warning('CRM contact deleted', [
                'contact_id' => $contact->id,
                'name' => $contact->name,
                'actor_id' => auth()->id(),
            ]);
        }

        return $deleted;
    }

    public function bulkDelete(array $ids): ?bool
    {
        return $this->repository->bulkDelete($ids);
    }

    public function findOrCreateFromLead(Lead $lead): ?Contact
    {
        if (! $lead->name && ! $lead->email) {
            return null;
        }

        if ($lead->email) {
            $existing = Contact::query()
                ->where('email', $lead->email)
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        return $this->create(ContactData::fromRequest([
            'company_id' => $lead->company_id,
            'name' => $lead->name ?: ($lead->company_name ?: __('crm::contact.fallback_name')),
            'email' => $lead->email,
            'phone' => $lead->phone,
            'job_title' => null,
            'notes' => $lead->problem_statement,
            'is_primary' => true,
        ]));
    }
}
