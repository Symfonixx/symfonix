<?php

namespace Modules\CRM\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\CRM\Models\ContactForm;

class ContactFormExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping
{
    public function collection()
    {
        return ContactForm::query()
            ->with('company:id,name')
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Company',
            'Name',
            'Email',
            'Mobile',
            'Subject',
            'Message',
            'IP Address',
            'Blocked',
            'Created At',
        ];
    }

    public function map($contact): array
    {
        return [
            $contact->id,
            $contact->company?->name,
            $contact->name,
            $contact->email,
            $contact->mobile,
            $contact->subject,
            $contact->message,
            $contact->ip_address,
            $contact->blocked ? 'Yes' : 'No',
            $contact->created_at ? $contact->created_at->format('Y-m-d H:i:s') : 'N/A',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 25,
            'C' => 20,
            'D' => 35,
            'E' => 15,
            'F' => 30,
            'G' => 50,
            'H' => 18,
            'I' => 12,
            'J' => 20,
        ];
    }
}
