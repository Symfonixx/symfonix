<?php

namespace Modules\User\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Modules\Base\Support\Meta;
use Modules\User\Http\Requests\UpdatePortalPasswordRequest;
use Modules\User\Http\Requests\UpdatePortalProfileRequest;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        return $this->inertia('User::Portal/Profile', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'avatar' => $user->avatar,
            ],
            'twoFactorEnabled' => $user->hasEnabledTwoFactorAuthentication(),
            'twoFactorPending' => ! empty($user->two_factor_secret) && is_null($user->two_factor_confirmed_at),
        ], (new Meta)
            ->title(__('user::portal.pages.profile_title'))
            ->description(__('user::portal.pages.profile_description'))
            ->robots('noindex, nofollow')
            ->toArray());
    }

    public function update(UpdatePortalProfileRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $validated = $request->validated();

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->mobile = $validated['mobile'] ?? $user->mobile;

        if ($request->hasFile('avatar')) {
            if ($user->img) {
                Storage::disk('public')->delete($user->img);
            }

            $user->img = $request->file('avatar')->store('avatars', 'public');
        }

        $user->save();

        return back()->with('success', __('user::portal.profile.updated'));
    }

    public function updatePassword(UpdatePortalPasswordRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->forceFill([
            'password' => Hash::make($request->validated('password')),
        ])->save();

        return back()->with('success', __('user::portal.profile.password_updated'));
    }
}
