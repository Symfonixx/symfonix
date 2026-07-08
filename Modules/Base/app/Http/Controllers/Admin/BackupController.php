<?php

namespace Modules\Base\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Base\Models\Settings;
use Modules\Base\Services\BackupService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class BackupController extends Controller
{
    public function __construct(protected BackupService $backupService)
    {
        $this->setActive('backups');
    }

    public function index()
    {
        $backups = $this->backupService->list();
        $settings = [
            'enabled' => filter_var(Settings::get('auto_backup_enabled', '0'), FILTER_VALIDATE_BOOLEAN),
            'interval_days' => (int) Settings::get('auto_backup_interval_days', '7'),
            'last_run' => Settings::get('auto_backup_last_run') ?: null,
        ];

        return view('base::admin.backups.index', compact('backups', 'settings'));
    }

    public function store(Request $request)
    {
        try {
            $this->backupService->create('manual');
            session()->flushMessage(true, __('base::backup.created'));
        } catch (Throwable $e) {
            $msg = config('app.debug') ? $e->getMessage() : __('base::backup.create_failed');
            session()->flushMessage(false, $msg, $e);
        }

        return back();
    }

    public function download(string $filename): BinaryFileResponse
    {
        $path = $this->backupService->absolutePath($filename);

        return response()->download($path, $filename);
    }

    public function destroy(string $filename)
    {
        try {
            $this->backupService->delete($filename);
            session()->flushMessage(true, __('base::backup.deleted'));
        } catch (Throwable $e) {
            $msg = config('app.debug') ? $e->getMessage() : __('base::backup.delete_failed');
            session()->flushMessage(false, $msg, $e);
        }

        return back();
    }
}
