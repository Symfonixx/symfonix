<?php

namespace Modules\User\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\User\Http\Requests\RoleRequest;
use Modules\User\Http\Requests\RoleUsersRequest;
use Modules\User\Repositories\Role\RoleRepository;
use Modules\User\Support\PermissionCatalog;

class RoleController extends Controller
{
    public function __construct(protected RoleRepository $roleRepository)
    {
        $this->setActive('hr');
        $this->setActive('roles');
    }

    public function index(): View
    {
        $roles = $this->roleRepository->all();
        $groups = PermissionCatalog::groups();

        return view('user::admin.role.index', compact('roles', 'groups'));
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        $this->roleRepository->store($request->input('role_name'), $request->input('permissions'));

        return back();
    }

    public function show(int $id): View
    {
        $groups = PermissionCatalog::groups();
        $role = $this->roleRepository->findById($id);
        $role->load([
            'permissions',
            'users' => fn ($query) => $query->where('type', User::TYPE_ADMIN),
        ]);
        $users = User::admins()
            ->whereDoesntHave('roles', function ($query) use ($id) {
                $query->where('id', $id);
            })->get();

        return view('user::admin.role.show', compact('role', 'users', 'groups'));
    }

    public function update(RoleRequest $request, int $id): RedirectResponse
    {
        $this->roleRepository->update($id, $request->input('role_name'), $request->input('permissions'));

        return back();
    }

    public function delete_role(int $id): RedirectResponse
    {
        $this->roleRepository->delete($id);

        return redirect()->route('admin.roles.index');
    }

    public function assignUsersToRole(RoleUsersRequest $request): RedirectResponse
    {
        $this->roleRepository->assignUsersToRole($request->input('role_id'), $request->input('user_ids'));

        return back();
    }

    public function removeUsersFromRole(RoleUsersRequest $request): RedirectResponse
    {
        $this->roleRepository->removeUsersFromRole($request->input('role_id'), $request->input('user_ids'));

        return back();
    }

    public function removeUserFromRole(Request $request): JsonResponse
    {
        $request->validate([
            'role_id' => 'required',
            'user_id' => 'required',
        ]);
        $this->roleRepository->removeUserFromRole($request->input('role_id'), $request->input('user_id'));

        return response()->json(['success' => __('The Operation Done Successfully')]);
    }
}
