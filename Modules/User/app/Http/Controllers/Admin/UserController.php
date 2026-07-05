<?php

namespace Modules\User\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\User\app\Data\UserData;
use Modules\User\app\Repositories\User\UserRepository;
use Modules\User\Http\Requests\StoreUserRequest;

class UserController extends Controller
{
    public function __construct(protected UserRepository $userRepository)
    {
        $this->setActive('crm');
        $this->setActive('customers');
    }

    public function index()
    {
        $model = $this->userRepository->all('customer');

        return view('user::.admin.user.index', compact('model'));
    }

    public function store(StoreUserRequest $request)
    {
        $userData = UserData::validateAndCreate($request->all());
        $user = $this->userRepository->store($userData);

        if ($request->filled('return_url') && $user) {
            return redirect($request->input('return_url').'?customer_id='.$user->id);
        }

        return redirect()->route('admin.customers.index');
    }

    public function update(Request $request, $id)
    {
        $user = $this->userRepository->find($id);
        $userData = UserData::validateAndCreate($request->all());
        $this->userRepository->update($userData, $user);

        return redirect()->route('admin.customers.index');
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
