<?php

namespace Modules\Cms\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Modules\Base\Support\Meta;
use Modules\Cms\Models\Page;
use Modules\Team\Models\Team;
use Modules\Testimonial\Models\Testimonial;

class PageController extends Controller {

    public function view($slug) {
        $page = Page::published()->where('slug', $slug)->firstOrFail();
        if (!session()->has('page') || session('page') !== $page->id) {
            $page->visits++;
            $page->save();
            session()->put('page', $page->id);
        }

        $meta = (new Meta())
            ->title($page->title)
            ->description($page->description)
            ->ogImage($page->image_link)
            ->twitterImage($page->image_link)
            ->toArray();

        return $this->inertia('Cms::PageShow', [
            'custom_page' => $page,
            'banner' => $page->image_link,
        ], $meta);
    }

    public function about_us() {
        $teams = Team::where('status', 'Published')
            ->latest()
            ->take(10)
            ->get();

        $testimonials = Testimonial::where('status', 'Published')
            ->latest()
            ->take(10)
            ->get();

        return $this->inertia('Cms::AboutUs', [
            'teams' => $teams,
            'testimonials' => $testimonials,
        ]);
    }

    public function privacy_policy() {
        return $this->inertia('Cms::PrivacyPolicy');
    }

    public function team() {
        $teams = Team::where('status', 'Published')
            ->latest()
            ->get();

        return $this->inertia('Cms::Team', [
            'teams' => $teams,
        ]);
    }

    public function testimonials() {
        $testimonials = Testimonial::where('status', 'Published')
            ->latest()
            ->get();

        return $this->inertia('Cms::Testimonials', [
            'testimonials' => $testimonials,
        ]);
    }

    public function pricing() {
        return $this->inertia('Cms::Pricing', []);
    }

    public function faq() {
        return $this->inertia('Cms::Faq', []);
    }
}
