<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Modules\Core\Http\Requests\DeleteMultiRequest;
use Modules\CRM\Actions\Company\BulkDeleteCompaniesAction;
use Modules\CRM\Actions\Company\CreateCompanyAction;
use Modules\CRM\Actions\Company\DeleteCompanyAction;
use Modules\CRM\Actions\Company\ListCompaniesAction;
use Modules\CRM\Actions\Company\UpdateCompanyAction;
use Modules\CRM\DTOs\Company\CompanyData;
use Modules\CRM\Http\Requests\CompanyIndexRequest;
use Modules\CRM\Http\Requests\StoreCompanyRequest;
use Modules\CRM\Http\Requests\UpdateCompanyRequest;
use Modules\CRM\Models\Company;

class CompanyController extends Controller
{
    public function __construct(
        private readonly ListCompaniesAction $listCompaniesAction,
        private readonly CreateCompanyAction $createCompanyAction,
        private readonly UpdateCompanyAction $updateCompanyAction,
        private readonly DeleteCompanyAction $deleteCompanyAction,
        private readonly BulkDeleteCompaniesAction $bulkDeleteCompaniesAction
    ) {
        $this->authorizeResource(Company::class, 'company');
        $this->setActive('crm');
        $this->setActive('companies');
    }

    public function index(CompanyIndexRequest $request)
    {
        $model = $this->listCompaniesAction->execute($request->validated());

        return view('crm::admin.company.index', compact('model'));
    }

    public function create()
    {
        $customers = $this->customers();

        return view('crm::admin.company.create', compact('customers'));
    }

    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        $data = CompanyData::fromRequest($request->validated());
        $this->createCompanyAction->execute($data);

        return redirect()->route('admin.companies.index');
    }

    public function show(Company $company)
    {
        $company->loadMissing([
            'user:id,name,email',
            'contacts' => fn ($query) => $query->latest(),
            'deals' => fn ($query) => $query->with('pipelineStage:id,name,color')->latest()->limit(20),
            'contactForms' => fn ($query) => $query->latest()->limit(10),
            'subscriptions' => fn ($query) => $query->latest(),
            'crmActivities.user:id,name',
            'crmAuditLogs.user:id,name',
        ]);

        return view('crm::admin.company.show', compact('company'));
    }

    public function edit(Company $company)
    {
        $customers = $this->customers();

        return view('crm::admin.company.edit', compact('company', 'customers'));
    }

    public function update(UpdateCompanyRequest $request, Company $company): RedirectResponse
    {
        $data = CompanyData::fromRequest($request->validated());
        $this->updateCompanyAction->execute($company, $data);

        return redirect()->route('admin.companies.index');
    }

    public function destroy(Company $company): RedirectResponse
    {
        $this->deleteCompanyAction->execute($company);

        return redirect()->route('admin.companies.index');
    }

    public function deleteMulti(DeleteMultiRequest $request): RedirectResponse
    {
        $this->bulkDeleteCompaniesAction->execute($request->input('ids', []));

        return back();
    }

    private function customers(): Collection
    {
        return User::query()
            ->customers()
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->get();
    }
}
