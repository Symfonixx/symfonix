<?php

namespace Modules\Reporting\DTOs;

use Carbon\CarbonInterface;

readonly class ReportFilters
{
    public function __construct(
        public string $period,
        public CarbonInterface $start,
        public CarbonInterface $end,
        public CarbonInterface $previousStart,
        public CarbonInterface $previousEnd,
        public string $label,
        public ?int $assigneeId = null,
        public ?int $employeeId = null,
        public ?int $categoryId = null,
        public ?string $currency = null,
    ) {}

    /**
     * @return array{from: string, to: string}
     */
    public function dateRange(): array
    {
        return [
            'from' => $this->start->toDateString(),
            'to' => $this->end->toDateString(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'period' => $this->period,
            'date_from' => $this->start->toDateString(),
            'date_to' => $this->end->toDateString(),
            'label' => $this->label,
            'assigned_to' => $this->assigneeId,
            'employee_id' => $this->employeeId,
            'category_id' => $this->categoryId,
            'currency' => $this->currency,
        ];
    }
}
