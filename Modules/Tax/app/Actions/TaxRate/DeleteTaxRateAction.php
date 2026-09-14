<?php

namespace Modules\Tax\Actions\TaxRate;

use Modules\Tax\Models\TaxRate;
use Modules\Tax\Services\TaxRate\TaxRateService;

class DeleteTaxRateAction
{
    public function __construct(private readonly TaxRateService $service) {}

    public function execute(TaxRate $taxRate): ?bool
    {
        return $this->service->delete($taxRate);
    }
}
