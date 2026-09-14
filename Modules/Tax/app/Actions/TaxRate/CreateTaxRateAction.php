<?php

namespace Modules\Tax\Actions\TaxRate;

use Modules\Tax\DTOs\TaxRate\TaxRateData;
use Modules\Tax\Models\TaxRate;
use Modules\Tax\Services\TaxRate\TaxRateService;

class CreateTaxRateAction
{
    public function __construct(private readonly TaxRateService $service) {}

    public function execute(TaxRateData $data): ?TaxRate
    {
        return $this->service->create($data);
    }
}
