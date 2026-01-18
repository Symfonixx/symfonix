<?php

namespace Modules\Support\app\Exports;

use Akaunting\Firewall\Models\Log as FirewallLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class FirewallLogExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths
{
    public function collection()
    {
        return FirewallLog::latest()->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'IP Address',
            'Level',
            'Middleware',
            'URL',
            'Referrer',
            'User ID',
            'Request Data',
            'Created At',
            'Updated At',
        ];
    }

    public function map($log): array
    {
        return [
            $log->id,
            $log->ip,
            ucfirst($log->level ?? 'medium'),
            $log->middleware ?? 'N/A',
            $log->url ?? 'N/A',
            $log->referrer ?? 'N/A',
            $log->user_id ?? 'N/A',
            $log->request ? substr($log->request, 0, 500) : 'N/A', // Limit request data to 500 chars
            $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : 'N/A',
            $log->updated_at ? $log->updated_at->format('Y-m-d H:i:s') : 'N/A',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // ID
            'B' => 18,  // IP Address
            'C' => 12,  // Level
            'D' => 20,  // Middleware
            'E' => 50,  // URL
            'F' => 40,  // Referrer
            'G' => 12,  // User ID
            'H' => 60,  // Request Data
            'I' => 20,  // Created At
            'J' => 20,  // Updated At
        ];
    }
}




