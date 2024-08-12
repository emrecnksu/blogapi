<?php

namespace App\Services;

use App\Repositories\CommentRepository;
use Illuminate\Support\Facades\Cache;

class CommentService
{
    protected $commentRepository;
    private $cacheDuration = 60;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function getComments($postSlug)
    {
        $cacheKey = $postSlug ? 'comments_post_' . $postSlug : 'comments_all';

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($postSlug) {
            return $this->commentRepository->getCommentsByPostSlug($postSlug);
        });
    }

    public function storeComment($post, $userId, $content)
    {
        $comment = $this->commentRepository->createComment($post->id, $userId, $content);
        $this->clearCache($post->slug);
        return $comment;
    }

    public function updateComment($comment, $content)
    {
        $this->commentRepository->updateComment($comment, $content);
        $this->clearCache($comment->post->slug);
        return $comment;
    }

    public function deleteComment($comment)
    {
        $slug = $comment->post->slug;
        $this->commentRepository->deleteComment($comment);
        $this->clearCache($slug);
    }

    public function approveComment($comment)
    {
        $this->commentRepository->approveComment($comment);
        $this->clearCache($comment->post->slug);
    }

    public function findCommentById($id)
    {
        return $this->commentRepository->findCommentById($id);
    }

    public function findPostBySlug($slug)
    {
        return $this->commentRepository->findPostBySlug($slug);
    }

    private function clearCache($postId = null)
    {
        Cache::forget('comments_all');
        if ($postId) {
            Cache::forget('comments_post_' . $postId);
        }
    }
}
