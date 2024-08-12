<?php

namespace App\Http\Controllers;

use App\Services\PostService;
use App\Http\Resources\PostResource;
use App\Traits\ResponseTrait;

class PostController
{
    use ResponseTrait;

    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index()
    {
        $posts = $this->postService->getAllPosts();
        return $this->successResponse(PostResource::collection($posts));
    }

    public function show($slug)
    {
        $post = $this->postService->getPostBySlug($slug);

        if (!$post) {
            return $this->errorResponse('Post bulunamadı', 404);
        }

        return $this->successResponse(new PostResource($post));
    }

    public function relatedPosts($slug)
    {
        $relatedData = $this->postService->getRelatedPosts($slug);

        return $this->successResponse([
            'relatedPosts' => PostResource::collection($relatedData['relatedPosts']),
            'isCategoryRelated' => $relatedData['isCategoryRelated'],
        ]);
    }
}
