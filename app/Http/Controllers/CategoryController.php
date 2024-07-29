<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController
{
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->get();

        return response()->json(['status' => 1, 'categories' => $categories], 200);
    }

    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['status' => 0, 'message' => 'Kategori bulunamadı'], 404);
        }

        return response()->json(['status' => 1, 'category' => $category], 200);
    }

    public function posts($id)
    {
        $category = Category::findOrFail($id);
        $posts = Post::where('category_id', $id)->where('status', true)->with('user', 'tags')->get();

        return response()->json([
            'status' => 1,
            'category' => $category,
            'posts' => $posts,
        ], 200);
    }
}

