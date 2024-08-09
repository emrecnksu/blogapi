<?php

namespace App\Services;

use App\Repositories\PostRepository;

class PostService
{
    protected $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function getAllPosts()
    {
        return $this->postRepository->getAllPosts();
    }

    public function getPostBySlug(string $slug)
    {
        return $this->postRepository->findBySlug($slug);
    }

    public function getRelatedPosts(string $slug)
    {
        return $this->postRepository->getRelatedPosts($slug);
    }
}
