<?php

namespace Modules\Cms\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Cms\Http\Requests\SaveBlogCategoryRequest;
use Modules\Cms\Models\BlogCategory;
use Modules\Cms\Repositories\BlogCategory\BlogCategoryRepository;
use Modules\Core\Http\Requests\DeleteMultiRequest;

class BlogCategoryController extends Controller
{
    protected BlogCategoryRepository $categoryRepository;

    public function __construct(BlogCategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->setActive('cms');
        $this->setActive('blogs_categories');
    }

    public function index()
    {
        $model = $this->categoryRepository->all(['id', 'name', 'slug', 'created_at']);

        return view('cms::admin.blog_category.index', compact('model'));
    }

    public function create()
    {
        return view('cms::admin.blog_category.create');
    }

    public function store(SaveBlogCategoryRequest $request): RedirectResponse
    {
        $this->categoryRepository->store($this->payload($request));

        return redirect()->route('admin.blogs_categories.index');
    }

    public function edit(BlogCategory $blogs_category)
    {
        return view('cms::admin.blog_category.edit', compact('blogs_category'));
    }

    public function update(SaveBlogCategoryRequest $request, BlogCategory $blogs_category): RedirectResponse
    {
        $this->categoryRepository->update($this->payload($request, $blogs_category->slug), $blogs_category);

        return redirect()->route('admin.blogs_categories.index');
    }

    public function deleteMulti(DeleteMultiRequest $request): RedirectResponse
    {
        $this->categoryRepository->deleteMulti($request->input('ids'));

        return back();
    }

    /**
     * @return array{name: string, slug: string, auto_translate: bool}
     */
    private function payload(SaveBlogCategoryRequest $request, ?string $slug = null): array
    {
        return [
            'name' => $request->validated('name'),
            'slug' => $slug ?? $request->validated('slug'),
            'auto_translate' => $request->boolean('auto_translate'),
        ];
    }
}
