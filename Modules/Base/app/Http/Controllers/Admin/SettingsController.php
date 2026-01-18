<?php

namespace Modules\Base\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Base\Models\Settings;
use Modules\Core\Traits\FileTrait;

class SettingsController extends Controller
{
    use FileTrait;

    public function __construct()
    {
        $this->setActive('settings');
    }

    public function index()
    {
        $this->setActive('websiteConfigurations');
        $settings = Settings::pluck('value', 'key');

        return view('base::admin.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        // Handle uploaded images
        if ($request->hasFile('imgs')) {
            foreach ($request->file('imgs') as $key => $file) {
                if (! $file) {
                    continue;
                }

                // Delete old file if it exists
                $oldFile = Settings::get($key) ?: null;
                if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                    Storage::disk('public')->delete($oldFile);
                }

                // Store new file on the public disk under /settings
                $path = $file->store('settings', 'public');

                if ($path) {
                    Settings::set($key, $path);
                }
            }
        }

        // Handle other data settings (including empty strings so fields can be cleared)
        $data = $request->input('data', []);
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                // Normalise null values to empty string so we can explicitly clear a setting
                if ($value === null) {
                    $value = '';
                }

                Settings::set($key, $value);
            }
        }

        cache()->forget('settings');
        session()->flushMessage(true);

        return back();
    }
}
