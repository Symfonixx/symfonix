<?php

namespace Modules\CRM\Services\Contact;

use Illuminate\Support\Facades\DB;
use Modules\CRM\DTOs\Contact\ContactData;
use Modules\CRM\Models\Contact;
use Modules\CRM\Models\ContactForm;
use Modules\CRM\Models\Lead;
use Modules\CRM\Support\AuditLogger;
use Modules\CRM\Models\CrmAuditLog;

class ContactFormConversionService
{
    public function __construct(
        private readonly ContactService $contactService,
    ) {}

    public function convertToLead(ContactForm $form): Lead
    {
        if ($form->lead_id) {
            abort(422, __('crm::contact_form.conversion.already_converted'));
        }

        return DB::transaction(function () use ($form) {
            $form->loadMissing(['company:id,name', 'services:services.id']);

            $serviceIds = $form->serviceIds();

            $lead = Lead::create([
                'name' => $form->name,
                'email' => $form->email,
                'phone' => $form->mobile,
                'company_id' => $form->company_id,
                'company_name' => $form->company?->name,
                'source' => Lead::SOURCE_WEBSITE,
                'problem_statement' => $form->message,
                'service_id' => $serviceIds[0] ?? null,
                'service_interest' => $serviceIds !== [] ? null : $form->subject,
                'ip_address' => $form->ip_address,
            ]);

            $lead->services()->sync($serviceIds);

            $contact = $this->findOrCreateContact($form);

            $form->update([
                'lead_id' => $lead->id,
                'contact_id' => $contact?->id,
                'converted_at' => now(),
            ]);

            AuditLogger::log($lead, CrmAuditLog::EVENT_CONVERTED, __('crm::contact_form.conversion.converted_to_lead'));

            session()->flushMessage(true);

            return $lead->fresh(['company']);
        });
    }

    public function convertToContact(ContactForm $form): Contact
    {
        if ($form->contact_id) {
            abort(422, __('crm::contact_form.conversion.already_has_contact'));
        }

        return DB::transaction(function () use ($form) {
            $contact = $this->findOrCreateContact($form);

            if (! $contact) {
                abort(422, __('crm::contact_form.conversion.contact_failed'));
            }

            $form->update([
                'contact_id' => $contact->id,
                'converted_at' => $form->converted_at ?? now(),
            ]);

            AuditLogger::log($contact, CrmAuditLog::EVENT_CONVERTED, __('crm::contact_form.conversion.converted_to_contact'));

            session()->flushMessage(true);

            return $contact->fresh(['company']);
        });
    }

    private function findOrCreateContact(ContactForm $form): ?Contact
    {
        if ($form->contact_id) {
            return Contact::query()->find($form->contact_id);
        }

        if ($form->email) {
            $existing = Contact::query()
                ->where('email', $form->email)
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        return $this->contactService->create(ContactData::fromRequest([
            'company_id' => $form->company_id,
            'name' => $form->name,
            'email' => $form->email,
            'phone' => $form->mobile,
            'job_title' => null,
            'notes' => trim(($form->subject ? $form->subject."\n\n" : '').$form->message),
            'is_primary' => false,
        ]));
    }
}
