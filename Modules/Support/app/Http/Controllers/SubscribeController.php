<?php

namespace Modules\Support\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Support\Models\Subscriber;

class SubscribeController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|max:255|unique:subscribers,email',
            ], [
                'email.required' => __('The email field is required.'),
                'email.email' => __('Please enter a valid email address.'),
                'email.unique' => __('This email is already subscribed.'),
            ]);

            Subscriber::create([
                'email' => $validated['email'],
                'ip_address' => $request->ip(),
                'lang' => app()->getLocale(),
                'blocked' => false,
            ]);
            session()->flushMessage(true, __('Thank you for subscribing to our newsletter!'));

            return back();
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flushMessage(false);

            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            session()->flushMessage(false, __('An error occurred. Please try again later.'));

            return back()->withErrors(['email' => __('An error occurred. Please try again later.')])->withInput();
        }
    }
}
