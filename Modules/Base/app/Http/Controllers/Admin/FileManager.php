<?php

namespace Modules\Base\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class FileManager extends Controller
{
    public function index()
    {
        $this->setActive('filemanager');

        return view('base::admin.filemanager.index');
    }
}
