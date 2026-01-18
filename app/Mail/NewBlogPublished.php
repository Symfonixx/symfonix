<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Cms\Models\Blog;

class NewBlogPublished extends Mailable
{
    use Queueable, SerializesModels;

    public Blog $blog;
    public string $locale;
    public string $title;

    public function __construct(Blog $blog, ?string $locale = null)
    {
        $this->blog = $blog;
        $this->locale = $locale ?: app()->getLocale();
        $this->title = $blog->getTranslation('title', $this->locale, true);
    }

    public function build(): self
    {
        return $this->subject("New blog: {$this->title}")
            ->markdown('emails.subscribers.new-blog', [
                'blog' => $this->blog,
                'title' => $this->title,
                'url' => route('blogs.show', $this->blog->slug),
            ]);
    }
}
