<?php

namespace Modules\Support\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\NewContactFormSubmitted;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Modules\Base\Models\Branch;
use Modules\Base\Models\Settings;
use Modules\Support\Models\Complaint;
use Modules\Support\Models\ContactForm;
use Modules\Support\Models\Subscriber;

class ContactUsController extends Controller {

    public function index() {

        $branches = Cache::rememberForever('branches', function () {
            return Branch::all();
        });

        return Inertia::render('Support::Index', [
            'branches' => $branches,
        ]);
    }

    public function store(Request $request) {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'mobile' => 'required|string|max:255',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|min:10',
            ], [
                'name.required' => __('The name field is required.'),
                'email.required' => __('The email field is required.'),
                'email.email' => __('Please enter a valid email address.'),
                'mobile.required' => __('The phone field is required.'),
                'subject.required' => __('The subject field is required.'),
                'message.required' => __('The message field is required.'),
            ]);

            $contact = ContactForm::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'],
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'ip_address' => $request->ip(),
                'blocked' => false,
            ]);

            $this->notifyAdmins($contact);
            session()->flushMessage(true, __('Thank you for contacting us! We will get back to you soon.'));
            return back();
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flushMessage(false);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            session()->flushMessage(false, __('An error occurred. Please try again later.'));
            return back()->withErrors(['message' => __('An error occurred. Please try again later.')])->withInput();
        }
    }

    public function subscribe(Request $request) {
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

    private function notifyAdmins(ContactForm $contact): void
    {
        $emails = $this->getAdminEmails();
        if (empty($emails)) {
            return;
        }

        try {
            Mail::to($emails)->send(new NewContactFormSubmitted($contact));
        } catch (\Throwable $e) {
            Log::error('Failed to send contact form admin notification.', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function getAdminEmails(): array
    {
        $emailSetting = Settings::get('email') ?: config('mail.from.address');
        if (! $emailSetting) {
            return [];
        }

        $emails = preg_split('/[,\s;]+/', $emailSetting);
        $emails = array_filter($emails, function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });

        return array_values(array_unique($emails));
    }
}

