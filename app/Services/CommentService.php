<?php

namespace App\Services;

use App\Models\Comment;
use App\Repositories\CommentRepository;

class CommentService
{
    protected $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function getCommentsByPostSlug(string $postSlug = null)
    {
        return $this->commentRepository->getCommentsByPostSlug($postSlug);
    }

    public function createComment(array $data): Comment
    {
        return $this->commentRepository->create($data);
    }

    public function updateComment(Comment $comment, array $data): bool
    {
        return $this->commentRepository->update($comment, $data);
    }

    public function deleteComment(Comment $comment): bool
    {
        return $this->commentRepository->delete($comment);
    }

    public function approveComment(Comment $comment, string $token): bool
    {
        if ($comment->approval_token === $token) {
            return $this->commentRepository->update($comment, ['approved' => true, 'approval_token' => null]);
        }
        return false;
    }

    public function findById($id): ?Comment
    {
        return $this->commentRepository->findById($id);
    }
}
