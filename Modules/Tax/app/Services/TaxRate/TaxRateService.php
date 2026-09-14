<?php

namespace Modules\Tax\Services\TaxRate;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Tax\DTOs\TaxRate\TaxRateData;
use Modules\Tax\Models\TaxRate;
use Modules\Tax\Repositories\TaxRate\TaxRateRepository;

class TaxRateService
{
    public function __construct(private readonly TaxRateRepository $repository) {}

    public function list(): Collection
    {
        return TaxRate::query()
            ->withCount('ledgerEntries')
            ->orderBy('name')
            ->get();
    }

    public function activeOptions(): Collection
    {
        return TaxRate::query()
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'percentage', 'type', 'region_code', 'is_default']);
    }

    public function create(TaxRateData $data): ?TaxRate
    {
        $taxRate = $this->repository->create($data);

        if ($taxRate) {
            Log::info('Tax rate created', [
                'tax_rate_id' => $taxRate->id,
                'name' => $taxRate->name,
                'actor_id' => auth()->id(),
            ]);
        }

        return $taxRate;
    }

    public function update(TaxRate $taxRate, TaxRateData $data): ?TaxRate
    {
        $updated = $this->repository->update($taxRate, $data);

        if ($updated) {
            Log::info('Tax rate updated', [
                'tax_rate_id' => $taxRate->id,
                'name' => $taxRate->name,
                'actor_id' => auth()->id(),
            ]);
        }

        return $updated;
    }

    public function delete(TaxRate $taxRate): ?bool
    {
        $deleted = $this->repository->delete($taxRate);

        if ($deleted) {
            Log::warning('Tax rate deleted', [
                'tax_rate_id' => $taxRate->id,
                'name' => $taxRate->name,
                'actor_id' => auth()->id(),
            ]);
        }

        return $deleted;
    }
}
