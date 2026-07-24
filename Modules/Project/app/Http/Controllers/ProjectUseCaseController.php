<?php

namespace Modules\Project\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Base\Models\Seo;
use Modules\Base\Support\Meta;
use Modules\Base\Support\Schema;
use Modules\Project\Models\ProjectUseCase;
use Modules\Project\Repositories\ProjectUseCase\ProjectUseCaseRepository;

class ProjectUseCaseController extends Controller
{
    public function __construct(
        private readonly ProjectUseCaseRepository $useCaseRepository,
    ) {}

    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $useCases = $this->useCaseRepository->publishedPaginate(9);

        $siteName = Seo::get('website_name', config('app.name'));
        $canonical = route('use-cases.index');
        $meta = (new Meta)
            ->title(__('project::use_case.pages.website_title').' | '.$siteName)
            ->description(__('project::use_case.meta.index_description'))
            ->keywords(__('project::use_case.meta.index_keywords'))
            ->ogImage()
            ->twitterImage()
            ->canonical($canonical)
            ->toArray();

        $listItems = $useCases->getCollection()->take(20)->map(function (ProjectUseCase $item) use ($locale) {
            return [
                'name' => $item->getTranslation('title', $locale) ?: $item->title,
                'url' => route('use-cases.show', ['slug' => $item->slug]),
            ];
        })->values()->all();

        return $this->inertia('Project::UseCaseIndex', [
            'useCases' => $useCases->through(fn (ProjectUseCase $item) => $this->mapUseCase($item, $locale)),
            'structuredData' => [
                Schema::breadcrumbs([
                    ['name' => __('Home'), 'url' => route('home')],
                    ['name' => __('project::use_case.pages.website_title'), 'url' => $canonical],
                ]),
                Schema::itemList(__('project::use_case.pages.website_title'), $listItems, $canonical),
            ],
        ], $meta);
    }

    public function show(string $slug)
    {
        $locale = app()->getLocale();
        $useCase = $this->useCaseRepository->findBySlug($slug);

        if (! session()->has('use_case_'.$useCase->id)) {
            $useCase->increment('visits');
            session()->put('use_case_'.$useCase->id, true);
        }

        $related = ProjectUseCase::query()
            ->published()
            ->where('id', '!=', $useCase->id)
            ->ordered()
            ->limit(3)
            ->get()
            ->map(fn (ProjectUseCase $item) => $this->mapUseCase($item, $locale));

        $canonical = route('use-cases.show', ['slug' => $useCase->slug]);

        $meta = (new Meta)
            ->title($useCase->getTranslation('title', $locale).' | '.Seo::get('website_name', config('app.name')))
            ->description(strip_tags($useCase->getTranslation('summary', $locale) ?: ''))
            ->keywords(implode(', ', $useCase->technologies ?? []))
            ->ogImage($useCase->image_link)
            ->twitterImage($useCase->image_link)
            ->type('article')
            ->canonical($canonical)
            ->toArray();

        $structuredData = [
            Schema::breadcrumbs([
                ['name' => __('Home'), 'url' => route('home')],
                ['name' => __('project::use_case.pages.website_title'), 'url' => route('use-cases.index')],
                ['name' => $useCase->getTranslation('title', $locale), 'url' => $canonical],
            ]),
            Schema::article([
                'type' => 'Article',
                'title' => $useCase->getTranslation('title', $locale),
                'description' => strip_tags($useCase->getTranslation('summary', $locale) ?: ''),
                'image' => $useCase->image_link,
                'url' => $canonical,
                'datePublished' => optional($useCase->created_at)->toAtomString(),
                'dateModified' => optional($useCase->updated_at)->toAtomString(),
                'keywords' => implode(', ', $useCase->technologies ?? []),
                'section' => $useCase->category_tag,
                'locale' => $locale,
            ]),
        ];

        return $this->inertia('Project::UseCaseShow', [
            'structuredData' => $structuredData,
            'useCase' => $this->mapUseCase($useCase, $locale, true),
            'relatedUseCases' => $related,
        ], $meta);
    }

    private function mapUseCase(ProjectUseCase $useCase, string $locale, bool $detailed = false): array
    {
        $data = [
            'id' => $useCase->id,
            'slug' => $useCase->slug,
            'title' => $useCase->getTranslation('title', $locale),
            'client_name' => $useCase->getTranslation('client_name', $locale),
            'summary' => $useCase->getTranslation('summary', $locale),
            'image_link' => $useCase->image_link,
            'technologies' => $useCase->technologies ?? [],
            'category_tag' => $useCase->category_tag,
            'project_url' => $useCase->project_url,
            'completed_year' => $useCase->completed_year,
            'featured' => $useCase->featured,
        ];

        if ($detailed) {
            $data['challenge'] = $useCase->getTranslation('challenge', $locale);
            $data['solution'] = $useCase->getTranslation('solution', $locale);
            $data['results'] = $useCase->getTranslation('results', $locale);
            $data['content'] = $useCase->getTranslation('content', $locale);
        }

        return $data;
    }
}
