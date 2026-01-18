<?php

namespace Modules\Cms\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Cms\Models\Blog;
use Modules\Cms\Repositories\Blog\BlogRepository;

class BlogController extends Controller
{
    protected BlogRepository $blogRepository;

    public function __construct(BlogRepository $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    }

    public function index(Request $request): JsonResponse
    {
        $columns = ['id', 'title', 'slug', 'image', 'status', 'featured', 'visits', 'category_id', 'created_at'];
        $paginator = $this->blogRepository->all($columns);

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function show(Blog $blog): JsonResponse
    {
        return response()->json(['data' => $blog]);
    }
}
