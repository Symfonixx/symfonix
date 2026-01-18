<?php

namespace Modules\Team\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Cms\Enums\CmsStatus;
use Modules\Core\Http\Requests\DeleteMultiRequest;
use Modules\Team\Data\TeamData;
use Modules\Team\Models\Team;
use Modules\Team\Repositories\TeamRepository;

class TeamController extends Controller
{
    protected TeamRepository $teamRepository;

    public function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
        $this->setActive('teams');
    }

    public function index()
    {
        $model = $this->teamRepository->all(['id', 'name', 'position', 'linked_in', 'facebook', 'github', 'behance', 'resume', 'key_skills', 'avatar', 'status', 'created_at']);

        return view('team::admin.team.index', compact('model'));
    }

    public function create()
    {
        return view('team::admin.team.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = TeamData::validate([
            'name' => $request->input('name'),
            'position' => $request->input('position'),
            'linked_in' => $request->input('linked_in'),
            'facebook' => $request->input('facebook'),
            'github' => $request->input('github'),
            'behance' => $request->input('behance'),
            'resume' => $request->file('resume'),
            'key_skills' => $request->input('key_skills'),
            'avatar' => $request->file('avatar'),
            'status' => $request->has('publish') ? CmsStatus::PUBLISHED : CmsStatus::ARCHIVED,
        ]);
        $this->teamRepository->store($data);

        return redirect()->route('admin.teams.index');
    }

    public function edit(Team $team)
    {
        return view('team::admin.team.edit', compact('team'));
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        $data = TeamData::validate([
            'name' => $request->input('name'),
            'position' => $request->input('position'),
            'linked_in' => $request->input('linked_in'),
            'facebook' => $request->input('facebook'),
            'github' => $request->input('github'),
            'behance' => $request->input('behance'),
            'resume' => $request->file('resume'),
            'key_skills' => $request->input('key_skills'),
            'avatar' => $request->file('avatar'),
            'status' => $request->has('publish') ? CmsStatus::PUBLISHED : CmsStatus::ARCHIVED,
        ]);
        $this->teamRepository->update($data, $team);

        return redirect()->route('admin.teams.index');
    }

    public function deleteMulti(DeleteMultiRequest $request): RedirectResponse
    {
        $this->teamRepository->deleteMulti($request->input('ids'));

        return back();
    }
}
