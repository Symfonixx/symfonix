<?php

namespace Modules\User\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Base\Support\FingerprintConfig;
use Modules\User\Models\AttendanceLog;
use Modules\User\Models\Employee;
use Modules\User\Services\Fingerprint\FingerprintAttendanceSyncService;
use Modules\User\Services\Fingerprint\FingerprintConnectionService;
use Modules\User\Services\Fingerprint\FingerprintEnrollmentService;

class FingerprintController extends Controller
{
    public function __construct(
        private readonly FingerprintConnectionService $connectionService,
        private readonly FingerprintEnrollmentService $enrollmentService,
        private readonly FingerprintAttendanceSyncService $attendanceSyncService,
    ) {
        $this->setActive('hr');
        $this->setActive('fingerprint');
    }

    public function index()
    {
        $logs = AttendanceLog::query()
            ->with('employee:id,name,email')
            ->latest('recorded_at')
            ->paginate(25);

        $stats = [
            'enrolled_employees' => Employee::query()->whereNotNull('fingerprint_enrolled_at')->count(),
            'active_employees' => Employee::query()->active()->count(),
            'total_logs' => AttendanceLog::query()->count(),
            'last_sync_at' => FingerprintConfig::get('fingerprint_last_sync_at'),
            'is_configured' => FingerprintConfig::isConfigured(),
        ];

        return view('user::admin.fingerprint.index', compact('logs', 'stats'));
    }

    public function testConnection(): JsonResponse
    {
        return response()->json($this->connectionService->testConnection());
    }

    public function enroll(Employee $employee, Request $request): JsonResponse
    {
        $fingerIndex = $request->filled('finger_index')
            ? (int) $request->input('finger_index')
            : null;

        if ($fingerIndex !== null && ($fingerIndex < 0 || $fingerIndex > 9)) {
            return response()->json([
                'success' => false,
                'message' => 'Finger index must be between 0 and 9.',
            ], 422);
        }

        return response()->json($this->enrollmentService->enroll($employee, $fingerIndex));
    }

    public function enrollAll(): JsonResponse
    {
        return response()->json($this->enrollmentService->enrollAllActive());
    }

    public function syncAttendance(): JsonResponse
    {
        return response()->json($this->attendanceSyncService->sync());
    }
}
