<?php

namespace Modules\User\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Modules\User\app\Data\UserData;
use Modules\User\app\Repositories\User\UserRepository;
use Modules\User\Http\Requests\StoreUserRequest;
use Modules\User\Http\Requests\UpdateUserRequest;

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

        return view('user::admin.user.index', compact('model'));
    }

    public function store(StoreUserRequest $request)
    {
        $userData = UserData::validateAndCreate([
            ...$request->safe()->only(['name', 'email', 'mobile', 'password']),
            'type' => User::TYPE_CUSTOMER,
        ]);
        $user = $this->userRepository->store($userData);

        if ($request->filled('return_url') && $user) {
            return redirect($request->input('return_url').'?customer_id='.$user->id);
        }

        return redirect()->route('admin.customers.index');
    }

    public function update(UpdateUserRequest $request, User $customer)
    {
        abort_if($customer->type !== User::TYPE_CUSTOMER, 404);

        $userData = UserData::validateAndCreate([
            ...$request->safe()->only(['name', 'email', 'mobile', 'password']),
            'type' => User::TYPE_CUSTOMER,
        ]);
        $this->userRepository->update($userData, $customer);

        return redirect()->route('admin.customers.index');
    }

    public function destroy(User $customer)
    {
        abort_if($customer->type !== User::TYPE_CUSTOMER, 404);

        $this->userRepository->delete($customer);

        return response()->json([
            'success' => true,
        ]);
    }
}
