<?php

namespace Modules\Base\Support;

use Modules\Base\Models\Seo;
use Modules\Base\Models\Settings;

class Meta
{
    protected array $data = [
        'title' => null,
        'description' => null,
        'keywords' => null,
        'robots' => 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1',
        'canonical' => null,
        'og' => [
            'title' => null,
            'description' => null,
            'image' => null,
            'type' => 'website',
        ],
        'twitter' => [
            'title' => null,
            'description' => null,
            'image' => null,
        ],
    ];

    public function title(?string $value = null): static
    {
        $seo_title = Seo::get('website_name', config('app.name'));
        $this->data['title'] = $value ?: $seo_title;
        $this->data['og']['title'] = $value ?: $seo_title;
        $this->data['twitter']['title'] = $value ?: $seo_title;

        return $this;
    }

    public function description(?string $value = null): static
    {
        $seo_desc = Seo::get('website_desc', '');
        $this->data['description'] = $value ?: $seo_desc;
        $this->data['og']['description'] = $value ?: $seo_desc;
        $this->data['twitter']['description'] = $value ?: $seo_desc;

        return $this;
    }

    public function keywords(?string $value = null): static
    {
        $seo_keywords = Seo::get('website_keywords', '');
        $this->data['keywords'] = $value ?: $seo_keywords;

        return $this;
    }

    public function ogImage(?string $url = null): static
    {
        $meta_img = Settings::get('meta_img');
        $this->data['og']['image'] = $url ?: $meta_img;

        return $this;
    }

    public function twitterImage(?string $url = null): static
    {
        $meta_img = Settings::get('meta_img');
        $this->data['twitter']['image'] = $url ?: $meta_img;

        return $this;
    }

    public function type(string $value = 'website'): static
    {
        $this->data['og']['type'] = $value;

        return $this;
    }

    public function canonical(?string $url = null): static
    {
        $this->data['canonical'] = $url ?: url()->current();

        return $this;
    }

    public function robots(string $value = 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1'): static
    {
        $this->data['robots'] = $value;

        return $this;
    }

    public function noindex(): static
    {
        $this->data['robots'] = 'noindex, nofollow';

        return $this;
    }

    public function toArray(): array
    {
        $this->data['canonical'] ??= url()->current();

        return $this->data;
    }
}
