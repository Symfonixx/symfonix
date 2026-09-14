<?php

namespace Modules\Tax\Repositories\TaxRate;

use Illuminate\Support\Collection;
use Modules\Tax\DTOs\TaxRate\TaxRateData;
use Modules\Tax\Models\TaxRate;

interface TaxRateRepository
{
    public function allOrdered(): Collection;

    public function create(TaxRateData $data): ?TaxRate;

    public function update(TaxRate $taxRate, TaxRateData $data): ?TaxRate;

    public function delete(TaxRate $taxRate): ?bool;
}
