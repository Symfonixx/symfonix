<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\CRM\Http\Requests\CalendarEventsRequest;
use Modules\CRM\Services\Calendar\CalendarService;

class CalendarController extends Controller
{
    public function __construct(
        private readonly CalendarService $calendarService,
    ) {
        $this->setActive('crm');
        $this->setActive('crm_calendar');
    }

    public function index(): View
    {
        return view('crm::admin.calendar.index');
    }

    public function events(CalendarEventsRequest $request): JsonResponse
    {
        $start = Carbon::parse($request->validated('start'))->startOfDay();
        $end = Carbon::parse($request->validated('end'))->endOfDay();
        $types = $request->has('types')
            ? array_values($request->validated('types') ?? [])
            : null;

        return response()->json(
            $this->calendarService->events($start, $end, $types)
        );
    }
}
