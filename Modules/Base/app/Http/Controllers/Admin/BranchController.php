<?php

namespace Modules\Base\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Log;
use Modules\Base\Models\Branch;
use Modules\Core\Http\Requests\DeleteMultiRequest;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->setActive('settings');
        $this->setActive('branches');
    }

    public function index()
    {
        $model = Branch::latest()->paginate(config('core.page_size', 10));

        return view('base::admin.branch.index', compact('model'));
    }

    public function create()
    {
        return view('base::admin.branch.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = [
            'name' => $request->input('name'),
            'city' => $request->input('city'),
            'address' => $request->input('address'),
            'phone' => $request->input('phone'),
        ];
        $this->prepareTranslatable($data);
        Branch::create($data);
        cache()->forget('branches');
        session()->flushMessage(true);

        return redirect()->route('admin.branches.index');
    }

    public function edit(Branch $branch)
    {
        return view('base::admin.branch.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $data = [
            'name' => $request->input('name'),
            'city' => $request->input('city'),
            'address' => $request->input('address'),
            'phone' => $request->input('phone'),
        ];
        $this->prepareTranslatable($data, $branch);
        $branch->update($data);
        session()->flushMessage(true);
cache()->forget('branches');
        return redirect()->route('admin.branches.index');
    }

    public function deleteMulti(DeleteMultiRequest $request): RedirectResponse
    {
        Branch::destroy($request->input('ids', []));
        cache()->forget('branches');
        session()->flushMessage(true);

        return back();
    }

    private function prepareTranslatable(array &$data, ?Branch $branch = null): void
    {
        // Only these fields are translatable. `phone` stays as plain text.
        $fields = ['name', 'city', 'address'];
        $locale = app()->getLocale();

        foreach ($fields as $field) {
            $val = $data[$field] ?? '';

            // For create, generate translations for all languages.
            // For update, only change the current locale and keep other locales as-is.
            if ($branch) {
                $trans = $branch->getTranslations($field);
                $trans[$locale] = $val;
            } else {
                $trans = [$locale => $val];
                foreach (otherLangs() as $lang) {
                    try {
                        $trans[$lang] = autoGoogleTranslator($lang, $val);
                    } catch (Exception $e) {
                        Log::error($e->getMessage());
                    }
                }
            }

            $data[$field] = $trans;
        }
    }
}



