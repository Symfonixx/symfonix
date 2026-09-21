<?php

namespace Modules\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\NewContactFormSubmitted;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Base\Models\Seo;
use Modules\Base\Support\AdminEmail;
use Modules\Base\Support\Meta;
use Modules\Base\Support\Schema;
use Modules\CRM\Http\Requests\StorePublicContactRequest;
use Modules\CRM\Models\ContactForm;

class ContactUsController extends Controller
{
    public function index()
    {
        $siteName = Seo::get('website_name', config('app.name'));
        $canonical = route('contact-us');
        $meta = (new Meta)
            ->title(__('Contact Us').' | '.$siteName)
            ->description(__('Contact our team for support, inquiries, or project discussions.'))
            ->keywords(__('contact, support, get in touch, customer service'))
            ->ogImage()
            ->twitterImage()
            ->canonical($canonical)
            ->toArray();

        return $this->inertia('CRM::Index', [
            'structuredData' => [
                Schema::breadcrumbs([
                    ['name' => __('Home'), 'url' => route('home')],
                    ['name' => __('Contact Us'), 'url' => $canonical],
                ]),
                Schema::webPage([
                    'type' => 'ContactPage',
                    'title' => __('Contact Us').' | '.$siteName,
                    'description' => __('Contact our team for support, inquiries, or project discussions.'),
                    'url' => $canonical,
                ]),
            ],
        ], $meta);
    }

    public function store(StorePublicContactRequest $request)
    {
        try {
            $validated = $request->validated();

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
        } catch (\Throwable $e) {
            report($e);
            session()->flushMessage(false, __('An error occurred. Please try again later.'));

            return back()->withErrors(['message' => __('An error occurred. Please try again later.')])->withInput();
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
        return AdminEmail::addresses();
    }
}
