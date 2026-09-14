<?php

namespace Modules\Base\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Modules\Base\Models\Seo;
use Modules\Base\Models\Settings;
use Modules\Team\Models\Team;

class HumansTxtController extends Controller
{
    /**
     * humans.txt — credits for the people behind the site.
     *
     * @see https://humanstxt.org/
     */
    public function index(): Response
    {
        $siteName = trim((string) Seo::get('website_name', 'Symfonix')) ?: 'Symfonix';
        $email = trim((string) Settings::get('email', 'hello@symfonix.io')) ?: 'hello@symfonix.io';
        $host = rtrim(request()->getSchemeAndHttpHost(), '/');

        $lines = [
            '/* TEAM */',
            'Developer: Hadi Hilal — Founder & Lead Developer',
            'Contact: '.$email,
            'Site: '.$host,
            '',
        ];

        $teams = $this->publishedTeamMembers();

        if ($teams->isNotEmpty()) {
            $lines[] = '/* TEAM MEMBERS */';

            foreach ($teams as $member) {
                $name = trim((string) $member->getTranslation('name', app()->getLocale(), false));
                $position = trim((string) $member->getTranslation('position', app()->getLocale(), false));

                if ($name === '') {
                    continue;
                }

                $line = $name;
                if ($position !== '') {
                    $line .= ' — '.$position;
                }

                $lines[] = $line;
            }

            $lines[] = '';
        }

        $lines[] = '/* SITE */';
        $lines[] = 'Standards: HTML5, CSS3, WCAG 2.1';
        $lines[] = 'Components: Laravel, Vue.js, Inertia.js';
        $lines[] = 'Software: PHP, MySQL, Vite';
        $lines[] = 'Last update: '.now()->format('Y/m/d');
        $lines[] = '';

        return response(
            implode("\n", $lines),
            200,
            [
                'Content-Type' => 'text/plain; charset=UTF-8',
                'Cache-Control' => 'public, max-age=86400',
            ]
        );
    }

    /**
     * @return Collection<int, Team>
     */
    private function publishedTeamMembers()
    {
        try {
            if (! class_exists(Team::class)) {
                return collect();
            }

            return Team::query()
                ->published()
                ->orderBy('id')
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }
}
