<?php

namespace Modules\User\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\User\app\Data\UserData;
use Modules\User\app\Repositories\User\UserRepository;
use Modules\User\Http\Requests\StoreUserRequest;

class AdminController extends Controller
{
    public function __construct(protected UserRepository $userRepository)
    {
        $this->setActive('hr');
        $this->setActive('admins');
    }

    public function index()
    {
        $model = $this->userRepository->all('admin');

        return view('user::.admin.admin.index', compact('model'));
    }

    public function show(User $admin)
    {
        abort_if($admin->type !== User::TYPE_ADMIN, 404);

        $eventTracks = $admin->adminEventTracks()->paginate(config('core.page_size'));

        return view('user::.admin.admin.show', compact('admin', 'eventTracks'));
    }

    public function store(StoreUserRequest $request)
    {
        $userData = UserData::validateAndCreate($request->all());
        $this->userRepository->store($userData);

        return redirect()->route('admin.admins.index');
    }

    public function update(Request $request, $id)
    {
        $user = $this->userRepository->find($id);
        $userData = UserData::validateAndCreate($request->all());
        $this->userRepository->update($userData, $user);

        return redirect()->route('admin.admins.index');
    }

    public function destroy($id)
    {
        $user = $this->userRepository->find($id);
        $this->userRepository->delete($user);

        return response()->json([
            'success' => true,
        ]);
    }
}
