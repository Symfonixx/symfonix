<?php

namespace Modules\Support\app\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Modules\Support\Models\Subscriber;

class SubscriberExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths
{
    public function collection()
    {
        return Subscriber::latest()->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Email',
            'IP Address',
            'Language',
            'Blocked',
            'Created At',
        ];
    }

    public function map($subscriber): array
    {
        return [
            $subscriber->id,
            $subscriber->email,
            $subscriber->ip_address,
            $subscriber->lang,
            $subscriber->blocked ? 'Yes' : 'No',
            $subscriber->created_at ? $subscriber->created_at->format('Y-m-d H:i:s') : 'N/A',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // ID
            'B' => 35,  // Email
            'C' => 18,  // IP Address
            'D' => 12,  // Language
            'E' => 12,  // Blocked
            'F' => 20,  // Created At
        ];
    }
}

