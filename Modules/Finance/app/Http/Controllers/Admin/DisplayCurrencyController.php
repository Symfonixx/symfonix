<?php

namespace Modules\Finance\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Finance\Services\CurrencyService;

class DisplayCurrencyController extends Controller
{
    public function update(Request $request, CurrencyService $currencyService): RedirectResponse
    {
        $validated = $request->validate([
            'currency' => ['required', 'string', 'size:3', 'in:'.implode(',', $currencyService->supportedCurrencies())],
        ]);

        $currencyService->setDisplayCurrency($validated['currency']);

        return back();
    }
}
