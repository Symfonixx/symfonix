<?php

namespace Modules\Tax\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Tax\Models\TaxLedgerEntry;
use Modules\Tax\Models\TaxRate;

class TaxLedgerController extends Controller
{
    public function __construct()
    {
        $this->setActive('tax_ledger');
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', TaxRate::class);

        $query = TaxLedgerEntry::query()
            ->with(['taxRate:id,name,percentage', 'company:id,name', 'project:id,title', 'subscription:id,name'])
            ->latest('transaction_date');

        if ($request->filled('direction')) {
            $query->where('direction', $request->string('direction')->toString());
        }

        if ($request->filled('from')) {
            $query->whereDate('transaction_date', '>=', $request->string('from')->toString());
        }

        if ($request->filled('to')) {
            $query->whereDate('transaction_date', '<=', $request->string('to')->toString());
        }

        $entries = $query->paginate((int) config('core.page_size', 15))->withQueryString();

        return view('tax::admin.ledger.index', compact('entries'));
    }
}
