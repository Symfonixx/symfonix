<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
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

    public function events(Request $request): JsonResponse
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after_or_equal:start'],
            'types' => ['sometimes', 'array'],
            'types.*' => ['string', 'in:activities,leads,deals,quotes,subscriptions,projects'],
        ]);

        $start = Carbon::parse($request->input('start'))->startOfDay();
        $end = Carbon::parse($request->input('end'))->endOfDay();
        $types = $request->has('types')
            ? array_values($request->input('types', []))
            : null;

        return response()->json(
            $this->calendarService->events($start, $end, $types)
        );
    }
}
