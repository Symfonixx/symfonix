<?php

namespace Modules\Cms\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Cms\Data\BlogData;
use Modules\Cms\Enums\CmsStatus;
use Modules\Cms\Models\Blog;
use Modules\Cms\Models\BlogCategory;
use Modules\Cms\Repositories\Blog\BlogRepository;
use Modules\Core\Http\Requests\DeleteMultiRequest;
use Modules\CRM\Services\Marketing\ContentMarketingEmailSender;

class BlogController extends Controller
{
    protected BlogRepository $blogRepository;

    public function __construct(
        BlogRepository $blogRepository,
        private readonly ContentMarketingEmailSender $contentMarketingEmailSender,
    ) {
        $this->blogRepository = $blogRepository;
        $this->setActive('cms');
        $this->setActive('blogs');
    }

    public function index()
    {
        $model = $this->blogRepository->all([
            'id', 'title', 'slug', 'image', 'status', 'featured', 'visits', 'category_id', 'created_at',
        ]);

        return view('cms::admin.blog.index', compact('model'));
    }

    public function create()
    {
        $categories = BlogCategory::all();

        return view('cms::admin.blog.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->contentMarketingEmailSender->validate($request);

        $this->blogRepository->store($this->payload($request));

        if ($this->contentMarketingEmailSender->shouldSend($request)) {
            try {
                $campaign = $this->contentMarketingEmailSender->send(
                    $request,
                    (string) $request->input('title'),
                    $this->contentMarketingEmailSender->buildBody(
                        $request->input('description'),
                        $request->input('content'),
                    ),
                );

                session()->flushMessage(
                    true,
                    __('crm::marketing.messages.queued', ['count' => $campaign->recipients_count]),
                );
            } catch (\Throwable $e) {
                report($e);
                session()->flushMessage(false, __('crm::marketing.messages.send_failed'));
            }
        }

        return redirect()->route('admin.blogs.index');
    }

    public function edit(Blog $blog)
    {
        $categories = BlogCategory::all();

        return view('cms::admin.blog.edit', compact('blog', 'categories'));
    }

    public function update(Request $request, Blog $blog): RedirectResponse
    {
        $this->blogRepository->update($this->payload($request, $blog->slug), $blog);

        return redirect()->route('admin.blogs.index');
    }

    public function deleteMulti(DeleteMultiRequest $request): RedirectResponse
    {
        $this->blogRepository->deleteMulti($request->input('ids'));

        return back();
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Request $request, ?string $slug = null): array
    {
        $data = BlogData::validate([
            'title' => $request->input('title'),
            'slug' => $slug ?? $request->input('slug'),
            'description' => $request->input('description'),
            'content' => $request->input('content'),
            'keywords' => $request->input('keywords'),
            'image' => $request->file('img'),
            'status' => $request->has('publish') ? CmsStatus::PUBLISHED : CmsStatus::ARCHIVED,
            'featured' => $request->boolean('featured'),
            'category_id' => $request->input('category_id'),
        ]);
        $data['auto_translate'] = $request->boolean('auto_translate');

        return $data;
    }
}
