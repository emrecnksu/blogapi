<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Cache;

class PostController
{
    private $cacheDuration = 60;

    public function index()
    {
        $cacheKey = 'posts_all';

        $posts = Cache::remember($cacheKey, $this->cacheDuration, function() {
            return Post::with(['category', 'user', 'tags'])->visible()->get();
        });

        return PostResource::collection($posts);
    }

    public function show($slug)
    {
        $cacheKey = 'post_'.$slug;

        $post = Cache::remember($cacheKey, $this->cacheDuration, function() use ($slug) {
            return Post::with(['category', 'user', 'tags'])->visible()->bySlug($slug)->firstOrFail();
        });

        if (!$post) {
            return response()->json(['status' => 0, 'message' => 'Post bulunamadı'], 404);
        }

        return new PostResource($post);
    }

    public function relatedPosts($slug)
    {
        $cacheKey = 'related_posts_'.$slug;

        $relatedPosts = Cache::remember($cacheKey, $this->cacheDuration, function() use ($slug) {
            $post = Post::bySlug($slug)->firstOrFail();
            $relatedPosts = Post::where('category_id', $post->category_id)
                                 ->where('id', '!=', $post->id)
                                 ->visible()
                                 ->take(3)
                                 ->with('category')
                                 ->get();

            $isCategoryRelated = true;
            if ($relatedPosts->count() < 3) {
                $additionalPosts = Post::where('id', '!=', $post->id)
                                       ->visible()
                                       ->take(3 - $relatedPosts->count())
                                       ->with('category')
                                       ->get();

                $relatedPosts = $relatedPosts->merge($additionalPosts);
                $isCategoryRelated = false;
            }

            return ['relatedPosts' => $relatedPosts, 'isCategoryRelated' => $isCategoryRelated];
        });

        return response()->json(['status' => 1, 'relatedPosts' => PostResource::collection($relatedPosts['relatedPosts']), 'isCategoryRelated' => $relatedPosts['isCategoryRelated']], 200);
    }
}
