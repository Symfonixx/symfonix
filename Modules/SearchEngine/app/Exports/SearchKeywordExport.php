<?php

namespace Modules\SearchEngine\app\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Modules\SearchEngine\Models\SearchKeyword;

class SearchKeywordExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths
{
    public function collection()
    {
        return SearchKeyword::orderBy('count', 'desc')->latest()->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Keyword',
            'Search Count',
            'Created At',
            'Updated At',
        ];
    }

    public function map($keyword): array
    {
        return [
            $keyword->id,
            $keyword->keyword,
            $keyword->count,
            $keyword->created_at ? $keyword->created_at->format('Y-m-d H:i:s') : 'N/A',
            $keyword->updated_at ? $keyword->updated_at->format('Y-m-d H:i:s') : 'N/A',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // ID
            'B' => 40,  // Keyword
            'C' => 15,  // Search Count
            'D' => 20,  // Created At
            'E' => 20,  // Updated At
        ];
    }
}

