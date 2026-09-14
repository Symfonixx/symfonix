<?php

namespace Modules\Tax\Repositories\TaxRate;

use Illuminate\Support\Collection;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\Tax\DTOs\TaxRate\TaxRateData;
use Modules\Tax\Models\TaxRate;

class TaxRateModelRepository implements TaxRateRepository
{
    use ExceptionHandlerTrait;

    public function allOrdered(): Collection
    {
        return TaxRate::query()->orderBy('name')->get();
    }

    public function create(TaxRateData $data): ?TaxRate
    {
        return $this->execute(function () use ($data) {
            if ($data->is_default) {
                $this->clearDefaultFlags($data->region_code);
            }

            $taxRate = TaxRate::query()->create($data->toArray());
            session()->flushMessage(true);

            return $taxRate;
        });
    }

    public function update(TaxRate $taxRate, TaxRateData $data): ?TaxRate
    {
        return $this->execute(function () use ($taxRate, $data) {
            if ($data->is_default) {
                $this->clearDefaultFlags($data->region_code, $taxRate->id);
            }

            $taxRate->update($data->toArray());
            session()->flushMessage(true);

            return $taxRate;
        });
    }

    public function delete(TaxRate $taxRate): ?bool
    {
        return $this->execute(function () use ($taxRate) {
            if ($taxRate->ledgerEntries()->exists()) {
                session()->flushMessage(false, __('tax::tax_rate.errors.in_use'));

                return false;
            }

            $deleted = $taxRate->delete();
            session()->flushMessage(true);

            return $deleted;
        });
    }

    private function clearDefaultFlags(?string $regionCode, ?int $exceptId = null): void
    {
        TaxRate::query()
            ->when($exceptId, fn ($query) => $query->where('id', '!=', $exceptId))
            ->when(
                $regionCode,
                fn ($query) => $query->where('region_code', $regionCode),
                fn ($query) => $query->whereNull('region_code'),
            )
            ->update(['is_default' => false]);
    }
}
