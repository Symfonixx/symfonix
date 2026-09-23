<?php

namespace Modules\CRM\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContactImportSampleExport implements FromArray, WithColumnWidths, WithHeadings
{
    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Phone',
            'Phone2',
            'Source',
            'Job Title',
            'Notes',
            'Is Primary',
            'Company',
            'Customer Email',
        ];
    }

    public function array(): array
    {
        return [
            [
                'John Smith',
                'john.smith@example.com',
                '+1 555 000 0001',
                '',
                'manual',
                'CTO',
                'Primary decision maker',
                'Yes',
                'Acme Corp',
                'customer@example.com',
            ],
            [
                'Jane Doe',
                'jane.doe@example.com',
                '+1 555 000 0002',
                '+1 555 000 0003',
                'referral',
                'Marketing Manager',
                '',
                'No',
                '',
                '',
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 35,
            'C' => 18,
            'D' => 18,
            'E' => 16,
            'F' => 20,
            'G' => 40,
            'H' => 12,
            'I' => 25,
            'J' => 35,
        ];
    }
}
