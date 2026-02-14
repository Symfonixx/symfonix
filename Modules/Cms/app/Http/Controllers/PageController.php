<?php

namespace Modules\Cms\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Base\Models\Seo;
use Modules\Base\Support\Meta;
use Modules\Cms\Models\Faq;
use Modules\Cms\Models\Page;
use Modules\Team\Models\Team;
use Modules\Testimonial\Models\Testimonial;

class PageController extends Controller
{
    public function view($slug)
    {
        $page = Page::published()->where('slug', $slug)->firstOrFail();
        if (! session()->has('page') || session('page') !== $page->id) {
            $page->visits++;
            $page->save();
            session()->put('page', $page->id);
        }

        $meta = (new Meta)
            ->title($page->title)
            ->description($page->description)
            ->keywords($page->keywords)
            ->ogImage($page->image_link)
            ->twitterImage($page->image_link)
            ->toArray();

        return $this->inertia('Cms::PageShow', [
            'custom_page' => $page,
            'banner' => $page->image_link,
        ], $meta);
    }

    public function about_us()
    {
        $teams = Team::where('status', 'Published')
            ->latest()
            ->take(10)
            ->inRandomOrder()
            ->get();

        $testimonials = Testimonial::where('status', 'Published')
            ->latest()
            ->take(10)
            ->get();

        $siteName = Seo::get('website_name', config('app.name'));
        $meta = (new Meta)
            ->title(__('About Us').' | '.$siteName)
            ->description(__('Learn about our team, mission, and the technology expertise behind our solutions.'))
            ->keywords(__('about us, IT consulting, technology experts, digital transformation'))
            ->ogImage()
            ->twitterImage()
            ->toArray();

        return $this->inertia('Cms::AboutUs', [
            'teams' => $teams,
            'testimonials' => $testimonials,
        ], $meta);
    }

    public function privacy_policy()
    {
        $siteName = Seo::get('website_name', config('app.name'));
        $meta = (new Meta)
            ->title(__('Privacy Policy').' | '.$siteName)
            ->description(__('Review how we collect, use, and protect your personal information.'))
            ->keywords(__('privacy policy, data protection, security, compliance'))
            ->ogImage()
            ->twitterImage()
            ->toArray();

        return $this->inertia('Cms::PrivacyPolicy', [], $meta);
    }

    public function team()
    {
        $siteName = Seo::get('website_name', config('app.name'));
        $meta = (new Meta)
            ->title(__('Our Members').' | '.$siteName)
            ->description(__('Meet the professionals behind our technology and consulting services.'))
            ->keywords(__('team, experts, leadership, professionals'))
            ->ogImage()
            ->twitterImage()
            ->toArray();
        $teams = Team::where('status', 'Published')
            ->latest()
            ->inRandomOrder()
            ->get();

        return $this->inertia('Cms::Team', [
            'teams' => $teams,
        ], $meta);
    }

    public function testimonials()
    {
        $siteName = Seo::get('website_name', config('app.name'));
        $meta = (new Meta)
            ->title(__('Testimonials').' | '.$siteName)
            ->description(__('Read what our clients say about working with our team.'))
            ->keywords(__('testimonials, reviews, client feedback, success stories'))
            ->ogImage()
            ->twitterImage()
            ->toArray();
        $testimonials = Testimonial::where('status', 'Published')
            ->latest()
            ->get();

        return $this->inertia('Cms::Testimonials', [
            'testimonials' => $testimonials,
        ], $meta);
    }

    public function pricing()
    {
        $siteName = Seo::get('website_name', config('app.name'));
        $meta = (new Meta)
            ->title(__('Pricing').' | '.$siteName)
            ->description(__('Compare our pricing plans and choose the right fit for your business.'))
            ->keywords(__('pricing, plans, packages, subscriptions'))
            ->ogImage()
            ->twitterImage()
            ->toArray();

        return $this->inertia('Cms::Pricing', [], $meta);
    }

    public function faq()
    {
        $siteName = Seo::get('website_name', config('app.name'));
        $meta = (new Meta)
            ->title(__('FAQs').' | '.$siteName)
            ->description(__('Find answers to common questions about our services and policies.'))
            ->keywords(__('FAQ, help center, support, common questions'))
            ->ogImage()
            ->twitterImage()
            ->toArray();

        $faqs = Faq::published()->ordered()->get();

        // Generate FAQ Schema for SEO (server-side)
        $faqSchema = null;
        if ($faqs->isNotEmpty()) {
            $locale = app()->getLocale();
            $mainEntity = $faqs->map(function ($faq) use ($locale) {
                // Get translation with fallback to English, then any available language
                $question = $faq->getTranslation('question', $locale)
                    ?: $faq->getTranslation('question', 'en')
                    ?: (is_array($faq->question) ? ($faq->question[$locale] ?? $faq->question['en'] ?? reset($faq->question)) : $faq->question);
                
                $answer = $faq->getTranslation('answer', $locale)
                    ?: $faq->getTranslation('answer', 'en')
                    ?: (is_array($faq->answer) ? ($faq->answer[$locale] ?? $faq->answer['en'] ?? reset($faq->answer)) : $faq->answer);

                if (empty($question) || empty($answer)) {
                    return null;
                }

                // Strip HTML tags and clean up entities
                $answerText = strip_tags($answer);
                $answerText = html_entity_decode($answerText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $answerText = preg_replace('/\s+/', ' ', $answerText);
                $answerText = trim($answerText);

                if (empty($answerText)) {
                    return null;
                }

                return [
                    '@type' => 'Question',
                    'name' => trim($question),
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $answerText
                    ]
                ];
            })->filter()->values();

            if ($mainEntity->isNotEmpty()) {
                $faqSchema = [
                    '@context' => 'https://schema.org',
                    '@type' => 'FAQPage',
                    'mainEntity' => $mainEntity->toArray()
                ];
            }
        }

        return $this->inertia('Cms::Faq', [
            'faqs' => $faqs,
            'faqSchema' => $faqSchema, // Pass schema to Blade template
        ], $meta);
    }
}
