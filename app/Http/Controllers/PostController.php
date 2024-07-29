<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PostController
{
    private $cacheDuration = 60;

    public function index()
    {
        $cacheKey = 'posts_all';

        $posts = Cache::remember($cacheKey, $this->cacheDuration, function() {
            return Post::with(['category', 'user', 'tags'])->where('status', true)->get();
        });

        return response()->json(['status' => 1, 'posts' => $posts], 200);
    }

    public function show($id)
    {
        $cacheKey = 'post_'.$id;

        $post = Cache::remember($cacheKey, $this->cacheDuration, function() use ($id) {
            return Post::with(['category', 'user', 'tags'])->findOrFail($id);
        });

        if (!$post) {
            return response()->json(['status' => 0, 'message' => 'Post bulunamadı'], 404);
        }

        return response()->json(['status' => 1, 'post' => $post], 200);
    }

    public function relatedPosts($id)
    {
        $cacheKey = 'related_posts_'.$id;

        $relatedPosts = Cache::remember($cacheKey, $this->cacheDuration, function() use ($id) {
            $post = Post::findOrFail($id);
            $relatedPosts = Post::where('category_id', $post->category_id)
                                 ->where('id', '!=', $id)
                                 ->where('status', true)
                                 ->take(3)
                                 ->with('category')
                                 ->get();

            $isCategoryRelated = true;
            if ($relatedPosts->count() < 3) {
                $additionalPosts = Post::where('id', '!=', $id)
                                       ->where('status', true)
                                       ->take(3 - $relatedPosts->count())
                                       ->with('category')
                                       ->get();

                $relatedPosts = $relatedPosts->merge($additionalPosts);
                $isCategoryRelated = false;
            }

            return ['relatedPosts' => $relatedPosts, 'isCategoryRelated' => $isCategoryRelated];
        });

        return response()->json(['status' => 1, 'relatedPosts' => $relatedPosts['relatedPosts'], 'isCategoryRelated' => $relatedPosts['isCategoryRelated']], 200);
    }
}
