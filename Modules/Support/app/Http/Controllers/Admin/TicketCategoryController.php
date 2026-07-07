<?php

namespace Modules\Support\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Support\app\Http\Requests\Admin\StoreTicketCategoryRequest;
use Modules\Support\Models\TicketCategory;

class TicketCategoryController extends Controller
{
    public function __construct()
    {
        $this->setActive('support');
        $this->setActive('ticket_categories');
    }

    public function index(): View
    {
        $model = TicketCategory::query()->ordered()->paginate(config('core.page_size'));

        return view('support::admin.ticket_category.index', compact('model'));
    }

    public function create(): View
    {
        return view('support::admin.ticket_category.create');
    }

    public function store(StoreTicketCategoryRequest $request): RedirectResponse
    {
        TicketCategory::create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', true),
        ]);

        session()->flushMessage(true);

        return redirect()->route('admin.ticket_categories.index');
    }

    public function edit(TicketCategory $ticketCategory): View
    {
        return view('support::admin.ticket_category.edit', ['category' => $ticketCategory]);
    }

    public function update(StoreTicketCategoryRequest $request, TicketCategory $ticketCategory): RedirectResponse
    {
        $ticketCategory->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', true),
        ]);

        session()->flushMessage(true);

        return redirect()->route('admin.ticket_categories.index');
    }
}
