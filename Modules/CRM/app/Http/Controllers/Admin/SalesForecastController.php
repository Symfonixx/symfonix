<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\CRM\Http\Requests\SalesForecastRequest;
use Modules\CRM\Services\Forecast\ForecastService;

class SalesForecastController extends Controller
{
    public function __construct(
        private readonly ForecastService $forecastService,
    ) {
        $this->setActive('crm');
        $this->setActive('sales_forecasts');
    }

    public function index(SalesForecastRequest $request)
    {
        $forecast = $this->forecastService->build($request->validated());

        return view('crm::admin.forecast.index', compact('forecast'));
    }

    public function data(SalesForecastRequest $request): JsonResponse
    {
        return response()->json(
            $this->forecastService->build($request->validated())
        );
    }
}
