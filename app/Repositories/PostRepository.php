<?php

namespace App\Repositories;

use App\Models\Post;

class PostRepository
{
    public function getAllPosts()
    {
        return Post::with(['category', 'user', 'tags'])->visible()->get();
    }

    public function findBySlug(string $slug): ?Post
    {
        return Post::with(['category', 'user', 'tags'])->visible()->bySlug($slug)->first();
    }

    public function getRelatedPosts(string $slug)
    {
        $post = Post::bySlug($slug)->firstOrFail();
        $relatedPosts = Post::where('category_id', $post->category_id)
                            ->where('id', '!=', $post->id)
                            ->visible()
                            ->take(3)
                            ->with('category')
                            ->get();

        if ($relatedPosts->count() < 3) {
            $additionalPosts = Post::where('id', '!=', $post->id)
                                   ->visible()
                                   ->take(3 - $relatedPosts->count())
                                   ->with('category')
                                   ->get();

            $relatedPosts = $relatedPosts->merge($additionalPosts);
        }

        return $relatedPosts;
    }
}
