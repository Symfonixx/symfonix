<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Modules\Core\Http\Requests\DeleteMultiRequest;
use Modules\CRM\Actions\Deal\BulkDeleteDealsAction;
use Modules\CRM\Actions\Deal\CreateDealAction;
use Modules\CRM\Actions\Deal\DeleteDealAction;
use Modules\CRM\Actions\Deal\ListDealsAction;
use Modules\CRM\Actions\Deal\ListDealsKanbanAction;
use Modules\CRM\Actions\Deal\MoveDealStageAction;
use Modules\CRM\Actions\Deal\UpdateDealAction;
use Modules\CRM\DTOs\Deal\DealData;
use Modules\CRM\Http\Requests\DealIndexRequest;
use Modules\CRM\Http\Requests\MoveDealStageRequest;
use Modules\CRM\Http\Requests\StoreDealRequest;
use Modules\CRM\Http\Requests\UpdateDealRequest;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\LeadTag;
use Modules\CRM\Repositories\PipelineStage\PipelineStageRepository;
use Modules\Finance\Services\FinanceService;
use Modules\Services\Models\Service;
use Modules\User\Support\EmployeeAccess;

class DealController extends Controller
{
    public function __construct(

        private readonly ListDealsAction $listDealsAction,

        private readonly ListDealsKanbanAction $listDealsKanbanAction,

        private readonly CreateDealAction $createDealAction,

        private readonly UpdateDealAction $updateDealAction,

        private readonly DeleteDealAction $deleteDealAction,

        private readonly BulkDeleteDealsAction $bulkDeleteDealsAction,

        private readonly MoveDealStageAction $moveDealStageAction,

        private readonly PipelineStageRepository $stageRepository,

        private readonly FinanceService $financeService,

    ) {

        $this->authorizeResource(Deal::class, 'deal');

        $this->setActive('crm');

        $this->setActive('deals');

    }

    public function index(DealIndexRequest $request)
    {

        $filters = $request->validated();

        $view = $filters['view'] ?? 'list';

        if ($view === 'kanban') {
            $this->setActive('pipeline');

            $stages = $this->listDealsKanbanAction->execute($filters);
            $tags = LeadTag::query()->active()->ordered()->get();

            return view('crm::admin.deal.kanban', compact('stages', 'filters', 'tags'));
        }

        $model = $this->listDealsAction->execute($filters);

        $stages = $this->stageRepository->allActive();
        $tags = LeadTag::query()->active()->ordered()->get();

        return view('crm::admin.deal.index', compact('model', 'stages', 'filters', 'tags'));
    }

    public function create()
    {

        return view('crm::admin.deal.create', $this->formData());

    }

    public function store(StoreDealRequest $request): RedirectResponse
    {

        $data = DealData::fromRequest($request->validated());

        $this->createDealAction->execute($data, $request->input('services', []));

        return redirect()->route('admin.deals.index');

    }

    public function show(Deal $deal)
    {

        $deal->loadMissing([

            'company:id,name',

            'pipelineStage:id,name,color',

            'assignee:id,name,email',

            'lead:id,name,email',

            'lead.tags',

            'services:id,title',

            'project:id,title,deal_id',

            'quotes:id,quote_number,status,total,currency,deal_id,created_at',

            'stageHistories.fromStage:id,name,color',

            'stageHistories.toStage:id,name,color',

            'stageHistories.changedBy:id,name',

            'crmActivities' => fn ($q) => $q->with('user:id,name')->latest()->limit(50),

            'crmAuditLogs' => fn ($q) => $q->with('user:id,name')->latest('created_at')->limit(50),

        ]);

        $stages = $this->stageRepository->allActive();

        $ledgerSummary = $deal->status === Deal::STATUS_WON

            ? $this->financeService->getDealLedgerSummary($deal)

            : null;

        return view('crm::admin.deal.show', compact('deal', 'stages', 'ledgerSummary'));

    }

    public function edit(Deal $deal)
    {

        $deal->loadMissing('services:id,title');

        return view('crm::admin.deal.edit', array_merge(['deal' => $deal], $this->formData($deal)));

    }

    public function update(UpdateDealRequest $request, Deal $deal): RedirectResponse
    {

        $data = DealData::fromRequest($request->validated());

        $this->updateDealAction->execute($deal, $data, $request->input('services', []));

        return redirect()->route('admin.deals.index');

    }

    public function destroy(Deal $deal): RedirectResponse
    {

        $this->deleteDealAction->execute($deal);

        return redirect()->route('admin.deals.index');

    }

    public function deleteMulti(DeleteMultiRequest $request): RedirectResponse
    {

        $this->bulkDeleteDealsAction->execute($request->input('ids', []));

        return back();

    }

    public function moveStage(MoveDealStageRequest $request, Deal $deal): RedirectResponse
    {

        $this->authorize('update', $deal);

        $validated = $request->validated();

        $this->moveDealStageAction->execute(

            $deal,

            (int) $validated['pipeline_stage_id'],

            $validated['notes'] ?? null

        );

        return back();

    }

    private function formData(?Deal $deal = null): array
    {

        return [

            'companies' => Company::query()->select(['id', 'name'])->orderBy('name')->get(),

            'stages' => $this->stageRepository->allActive(),

            'assignees' => $this->assignees(),

            'defaultStageId' => $this->stageRepository->findDefault()?->id,

            'services' => Service::query()

                ->published()

                ->select(['id', 'title'])

                ->orderBy('title')

                ->get(),

            'dealServices' => $this->dealServiceLines($deal),

        ];

    }

    private function dealServiceLines(?Deal $deal = null): array
    {

        $lines = old('services');

        if ($lines === null && $deal?->relationLoaded('services')) {

            $lines = $deal->services->map(fn ($service) => [

                'service_id' => $service->id,

                'quantity' => $service->pivot->quantity,

                'unit_price' => $service->pivot->unit_price,

            ])->values()->all();

        }

        return $lines ?: [['service_id' => '', 'quantity' => 1, 'unit_price' => '']];

    }

    private function assignees(): Collection
    {

        return EmployeeAccess::assignableQuery()

            ->select(['id', 'name', 'email'])

            ->get();

    }
}
