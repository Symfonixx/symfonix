<?php

namespace Modules\Support\app\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Modules\Support\Models\Complaint;

class ComplaintExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths
{
    protected $status;

    public function __construct($status = null)
    {
        $this->status = $status;
    }

    public function collection()
    {
        $query = Complaint::with('branch')->latest();
        
        if ($this->status && in_array($this->status, ['pending', 'resolved'])) {
            $query->where('status', $this->status);
        }
        
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Mobile',
            'Branch',
            'Description',
            'Status',
            'Resolved At',
            'IP Address',
            'Blocked',
            'Created At',
        ];
    }

    public function map($complaint): array
    {
        return [
            $complaint->id,
            $complaint->name,
            $complaint->mobile,
            $complaint->branch?->name ?? 'N/A',
            $complaint->description,
            ucfirst($complaint->status),
            $complaint->resolved_at ? $complaint->resolved_at->format('Y-m-d H:i:s') : 'N/A',
            $complaint->ip_address,
            $complaint->blocked ? 'Yes' : 'No',
            $complaint->created_at ? $complaint->created_at->format('Y-m-d H:i:s') : 'N/A',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // ID
            'B' => 20,  // Name
            'C' => 15,  // Mobile
            'D' => 25,  // Branch
            'E' => 50,  // Description
            'F' => 15,  // Status
            'G' => 20,  // Resolved At
            'H' => 18,  // IP Address
            'I' => 12,  // Blocked
            'J' => 20,  // Created At
        ];
    }
}

