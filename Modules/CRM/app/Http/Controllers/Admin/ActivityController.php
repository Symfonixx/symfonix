<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\CRM\Actions\Activity\CreateActivityAction;
use Modules\CRM\Actions\Activity\DeleteActivityAction;
use Modules\CRM\DTOs\Activity\ActivityData;
use Modules\CRM\Http\Requests\StoreActivityRequest;
use Modules\CRM\Models\CrmActivity;
use Modules\CRM\Support\CrmSubjectResolver;

class ActivityController extends Controller
{
    public function __construct(
        private readonly CreateActivityAction $createActivityAction,
        private readonly DeleteActivityAction $deleteActivityAction,
    ) {
        $this->setActive('crm');
    }

    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $this->authorize('create', CrmActivity::class);

        $validated = $request->validated();
        $subject = CrmSubjectResolver::resolve($validated['subject_type'], (int) $validated['subject_id']);
        $data = ActivityData::fromRequest($validated);

        $this->createActivityAction->execute($subject, $data);

        return back();
    }

    public function destroy(CrmActivity $activity): RedirectResponse
    {
        $this->authorize('delete', $activity);

        $this->deleteActivityAction->execute($activity);

        return back();
    }
}
