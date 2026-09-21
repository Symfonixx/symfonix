<?php

namespace Modules\Finance\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Finance\Http\Requests\UpdateDisplayCurrencyRequest;
use Modules\Finance\Services\CurrencyService;

class DisplayCurrencyController extends Controller
{
    public function update(UpdateDisplayCurrencyRequest $request, CurrencyService $currencyService): RedirectResponse
    {
        $currencyService->setDisplayCurrency($request->validated('currency'));

        return back();
    }
}
