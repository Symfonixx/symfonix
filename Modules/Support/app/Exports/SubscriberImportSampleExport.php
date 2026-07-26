<?php

namespace Modules\Support\app\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SubscriberImportSampleExport implements FromArray, WithColumnWidths, WithHeadings
{
    public function headings(): array
    {
        return [
            'Email',
            'IP Address',
            'Language',
            'Blocked',
        ];
    }

    public function array(): array
    {
        return [
            ['user@example.com', '192.168.1.1', 'en', 'No'],
            ['jane@example.com', '10.0.0.1', 'ar', 'Yes'],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35,  // Email
            'B' => 18,  // IP Address
            'C' => 12,  // Language
            'D' => 12,  // Blocked
        ];
    }
}
