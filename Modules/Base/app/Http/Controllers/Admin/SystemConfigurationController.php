<?php

namespace Modules\Base\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Base\Models\Settings;
use Modules\Core\Traits\FileTrait;

class SystemConfigurationController extends Controller
{
    use FileTrait;

    public function __construct()
    {
        $this->setActive('settings');
    }

    public function index()
    {
        $this->setActive('systemConfigurations');
        $settings = Settings::pluck('value', 'key');

        return view('base::admin.system-configurations.index', compact('settings'));
    }

    public function store(Request $request)
    {
        if ($request->hasFile('imgs')) {
            foreach ($request->file('imgs') as $key => $file) {
                if (! $file) {
                    continue;
                }

                $oldFile = Settings::get($key) ?: null;
                if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                    Storage::disk('public')->delete($oldFile);
                }

                $path = $file->store('settings', 'public');

                if ($path) {
                    Settings::set($key, $path);
                }
            }
        }

        $data = $request->input('data', []);
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                Settings::set($key, $value === null ? '' : $value);
            }
        }

        cache()->forget('settings');
        session()->flushMessage(true);

        return back();
    }
}
