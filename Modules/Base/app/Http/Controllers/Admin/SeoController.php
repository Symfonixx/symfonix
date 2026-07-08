<?php

namespace Modules\Base\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Base\Models\Seo;
use Modules\Base\Models\Settings;

class SeoController extends Controller
{
    public function __construct()
    {
        $this->setActive('settings');
    }

    public function index()
    {
        $this->setActive('seo');
        $seo = Seo::pluck('value', 'key');
        $metaImage = Settings::get('meta_img')
            ?: Settings::get('site_logo')
            ?: 'default.jpg';

        return view('base::admin.seo.index', compact('seo', 'metaImage'));
    }

    public function store(Request $request)
    {
        $data = $request->input('data', []);
        $autoTranslate = $request->boolean('auto_translate');
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                Seo::set($key, $value === null ? '' : $value, $autoTranslate);
            }
        }
        session()->flushMessage(true);

        return back();
    }
}
