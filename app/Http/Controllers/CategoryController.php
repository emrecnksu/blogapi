<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Http\Resources\CategoryResource;

class CategoryController
{
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->get();

        return CategoryResource::collection($categories);
    }

    public function show($slug)
    {
        $category = Category::where('slug', $slug)->first();

        if (!$category) {
            return response()->json(['status' => 0, 'message' => 'Kategori bulunamadı'], 404);
        }

        return new CategoryResource($category);
    }

    public function posts($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $posts = Post::where('category_id', $category->id)->visible()->with('user', 'tags')->get();

        return response()->json([
            'status' => 1,
            'data' => [
                'category' => new CategoryResource($category),
                'posts' => PostResource::collection($posts),
            ]
        ], 200);
    }
}
