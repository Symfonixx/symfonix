<?php

namespace Modules\Reporting\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportService
{
    /**
     * @param  array<string, mixed>  $report
     */
    public function exportCsv(array $report, string $department, string $from, string $to): StreamedResponse
    {
        $filename = "{$department}-report-{$from}-to-{$to}.csv";
        $rows = $this->flattenForCsv($report, $department);

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');

            if ($rows !== []) {
                fputcsv($handle, array_keys($rows[0]));

                foreach ($rows as $row) {
                    fputcsv($handle, $row);
                }
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * @param  array<string, mixed>  $report
     */
    public function exportPdf(array $report, string $department, string $title, string $from, string $to): Response
    {
        $pdf = Pdf::loadView('reporting::admin.pdf.report', [
            'report' => $report,
            'department' => $department,
            'title' => $title,
            'from' => $from,
            'to' => $to,
        ])->setPaper('a4', 'landscape');

        return $pdf->download("{$department}-report-{$from}-to-{$to}.pdf");
    }

    /**
     * @param  array<string, mixed>  $report
     * @return array<int, array<string, mixed>>
     */
    private function flattenForCsv(array $report, string $department): array
    {
        $rows = [];

        foreach ($report['kpis'] ?? [] as $key => $kpi) {
            $rows[] = [
                'metric' => $key,
                'value' => $kpi['value'] ?? 0,
                'previous' => $kpi['previous'] ?? 0,
                'change' => $kpi['change'] ?? '',
            ];
        }

        $tableKey = match ($department) {
            'finance' => 'expense_categories',
            'sales' => 'rep_leaderboard',
            'marketing' => 'lead_sources',
            'operations' => 'open_tickets',
            'employee' => 'employee_project_performance',
            default => null,
        };

        if ($tableKey && ! empty($report['tables'][$tableKey])) {
            foreach ($report['tables'][$tableKey] as $row) {
                $rows[] = is_array($row) ? $row : (array) $row;
            }
        }

        return $rows;
    }
}
