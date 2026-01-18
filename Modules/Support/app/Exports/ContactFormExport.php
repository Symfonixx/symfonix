<?php

namespace Modules\Support\app\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Modules\Support\Models\ContactForm;

class ContactFormExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths
{
    public function collection()
    {
        return ContactForm::latest()->get();
    }

    public function headings(): array
    {
        return [
            'ID',
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
            'A' => 10,  // ID
            'B' => 20,  // Name
            'C' => 35,  // Email
            'D' => 15,  // Mobile
            'E' => 30,  // Subject
            'F' => 50,  // Message
            'G' => 18,  // IP Address
            'H' => 12,  // Blocked
            'I' => 20,  // Created At
        ];
    }
}

