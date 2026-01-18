<?php

namespace Modules\Support\app\Services;

use App\Mail\NewBlogPublished;
use App\Mail\NewServicePublished;
use Illuminate\Support\Facades\Mail;
use Modules\Cms\Models\Blog;
use Modules\Services\Models\Service;
use Modules\Support\Models\Subscriber;

class SubscriberNotificationService
{
    public function notifyNewBlog(Blog $blog): void
    {
        if ($blog->status !== 'Published') {
            return;
        }

        $this->notifySubscribers(function (Subscriber $subscriber) use ($blog) {
            $locale = $subscriber->lang ?: app()->getLocale();

            return new NewBlogPublished($blog, $locale);
        });
    }

    public function notifyNewService(Service $service): void
    {
        if ($service->status !== 'Published') {
            return;
        }

        $this->notifySubscribers(function (Subscriber $subscriber) use ($service) {
            $locale = $subscriber->lang ?: app()->getLocale();

            return new NewServicePublished($service, $locale);
        });
    }

    /**
     * @param  callable  $mailableFactory  function(Subscriber $subscriber): \Illuminate\Mail\Mailable
     */
    private function notifySubscribers(callable $mailableFactory): void
    {
        Subscriber::query()
            ->where('blocked', false)
            ->select(['id', 'email', 'lang'])
            ->chunkById(100, function ($subscribers) use ($mailableFactory) {
                foreach ($subscribers as $subscriber) {
                    if (! $subscriber->email) {
                        continue;
                    }

                    Mail::to($subscriber->email)->send($mailableFactory($subscriber));
                }
            });
    }
}
