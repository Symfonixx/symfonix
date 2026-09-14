<?php

namespace Modules\Tax\Actions\TaxRate;

use Illuminate\Support\Collection;
use Modules\Tax\Services\TaxRate\TaxRateService;

class ListTaxRatesAction
{
    public function __construct(private readonly TaxRateService $service) {}

    public function execute(): Collection
    {
        return $this->service->list();
    }
}
