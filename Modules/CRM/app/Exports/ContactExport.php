<?php

namespace Modules\CRM\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\CRM\Models\Contact;

class ContactExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping
{
    public function collection()
    {
        return Contact::query()
            ->with(['company:id,name', 'customer:id,email'])
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Phone2',
            'Source',
            'Job Title',
            'Company',
            'Customer Email',
            'Notes',
            'Is Primary',
            'Created At',
        ];
    }

    public function map($contact): array
    {
        return [
            $contact->id,
            $contact->name,
            $contact->email,
            $contact->phone,
            $contact->phone2,
            $contact->source,
            $contact->job_title,
            $contact->company?->name,
            $contact->customer?->email,
            $contact->notes,
            $contact->is_primary ? 'Yes' : 'No',
            $contact->created_at ? $contact->created_at->format('Y-m-d H:i:s') : 'N/A',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 25,
            'C' => 35,
            'D' => 18,
            'E' => 18,
            'F' => 16,
            'G' => 20,
            'H' => 25,
            'I' => 35,
            'J' => 40,
            'K' => 12,
            'L' => 20,
        ];
    }
}
