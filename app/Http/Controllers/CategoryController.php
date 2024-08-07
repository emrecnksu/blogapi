<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Http\Resources\PostResource;
use App\Http\Resources\CategoryResource;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class CategoryController
{
    use ResponseTrait;

    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->get();

        return $this->successResponse(CategoryResource::collection($categories));
    }

    public function show($slug)
    {
        $category = Category::bySlug($slug)->first();

        if (!$category) {
            return $this->errorResponse('Kategori bulunamadı', 404);
        }

        return $this->successResponse(new CategoryResource($category));
    }

    public function posts($slug)
    {
        $category = Category::bySlug($slug)->firstOrFail();
        $posts = Post::where('category_id', $category->id)->visible()->with('user', 'tags')->get();

        return $this->successResponse([
            'category' => new CategoryResource($category),
            'posts' => PostResource::collection($posts),
        ]);
    }
}
