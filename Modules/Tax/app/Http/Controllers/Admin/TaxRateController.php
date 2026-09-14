<?php

namespace Modules\Tax\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Tax\Actions\TaxRate\CreateTaxRateAction;
use Modules\Tax\Actions\TaxRate\DeleteTaxRateAction;
use Modules\Tax\Actions\TaxRate\ListTaxRatesAction;
use Modules\Tax\Actions\TaxRate\UpdateTaxRateAction;
use Modules\Tax\DTOs\TaxRate\TaxRateData;
use Modules\Tax\Http\Requests\StoreTaxRateRequest;
use Modules\Tax\Http\Requests\UpdateTaxRateRequest;
use Modules\Tax\Models\TaxRate;

class TaxRateController extends Controller
{
    public function __construct(
        private readonly ListTaxRatesAction $listTaxRatesAction,
        private readonly CreateTaxRateAction $createTaxRateAction,
        private readonly UpdateTaxRateAction $updateTaxRateAction,
        private readonly DeleteTaxRateAction $deleteTaxRateAction,
    ) {
        $this->authorizeResource(TaxRate::class, 'rate');
        $this->setActive('tax_rates');
    }

    public function index(): View
    {
        $taxRates = $this->listTaxRatesAction->execute();

        return view('tax::admin.tax_rate.index', compact('taxRates'));
    }

    public function create(): View
    {
        return view('tax::admin.tax_rate.create');
    }

    public function store(StoreTaxRateRequest $request): RedirectResponse
    {
        $data = TaxRateData::fromRequest($request->validated());
        $this->createTaxRateAction->execute($data);

        return redirect()->route('admin.tax.rates.index');
    }

    public function edit(TaxRate $rate): View
    {
        return view('tax::admin.tax_rate.edit', ['taxRate' => $rate]);
    }

    public function update(UpdateTaxRateRequest $request, TaxRate $rate): RedirectResponse
    {
        $data = TaxRateData::fromRequest($request->validated());
        $this->updateTaxRateAction->execute($rate, $data);

        return redirect()->route('admin.tax.rates.index');
    }

    public function destroy(TaxRate $rate): RedirectResponse
    {
        $this->deleteTaxRateAction->execute($rate);

        return redirect()->route('admin.tax.rates.index');
    }
}
