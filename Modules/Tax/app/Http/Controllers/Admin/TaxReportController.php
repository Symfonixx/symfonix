<?php

namespace Modules\Tax\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\CRM\Models\Company;
use Modules\Tax\Http\Requests\TaxReportRequest;
use Modules\Tax\Models\TaxRate;
use Modules\Tax\Services\TaxFilingReportService;
use Modules\Tax\Services\TaxRate\TaxRateService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TaxReportController extends Controller
{
    public function __construct(
        private readonly TaxFilingReportService $reportService,
        private readonly TaxRateService $taxRateService,
    ) {
        $this->setActive('tax_reports');
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', TaxRate::class);

        $filters = [
            'from' => $request->string('from')->toString() ?: now()->startOfMonth()->toDateString(),
            'to' => $request->string('to')->toString() ?: now()->endOfMonth()->toDateString(),
            'tax_rate_id' => $request->integer('tax_rate_id') ?: null,
            'company_id' => $request->integer('company_id') ?: null,
        ];

        $report = $this->reportService->generate(
            $filters['from'],
            $filters['to'],
            $filters['tax_rate_id'],
            $filters['company_id'],
        );

        return view('tax::admin.report.index', [
            'report' => $report,
            'filters' => $filters,
            'taxRates' => $this->taxRateService->list(),
            'companies' => Company::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function export(TaxReportRequest $request): StreamedResponse
    {
        $this->authorize('viewAny', TaxRate::class);

        $filters = $request->validated();
        $report = $this->reportService->generate(
            $filters['from'],
            $filters['to'],
            isset($filters['tax_rate_id']) ? (int) $filters['tax_rate_id'] : null,
            isset($filters['company_id']) ? (int) $filters['company_id'] : null,
        );

        $filename = 'tax-filing-'.$filters['from'].'-to-'.$filters['to'].'.csv';

        return response()->streamDownload(function () use ($report) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Direction', 'Tax Rate', 'Amount', 'Currency', 'Company', 'Project', 'Description']);

            foreach ($report['entries'] as $entry) {
                fputcsv($handle, [
                    $entry->transaction_date->toDateString(),
                    $entry->direction,
                    $entry->taxRate?->name,
                    $entry->amount,
                    $entry->currency,
                    $entry->company?->name,
                    $entry->project?->title,
                    $entry->description,
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
