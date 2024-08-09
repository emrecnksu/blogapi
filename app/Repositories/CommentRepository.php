<?php

namespace App\Repositories;

use App\Models\Comment;

class CommentRepository
{
    public function create(array $data): Comment
    {
        return Comment::create($data);
    }

    public function findById($id): ?Comment
    {
        return Comment::find($id);
    }

    public function update(Comment $comment, array $data): bool
    {
        return $comment->update($data);
    }

    public function delete(Comment $comment): bool
    {
        return $comment->delete();
    }

    public function getCommentsByPostSlug(string $postSlug = null)
    {
        $commentsQuery = Comment::where('approved', true);

        if ($postSlug) {
            $post = Post::where('slug', $postSlug)->firstOrFail();
            $commentsQuery = $commentsQuery->where('post_id', $post->id);
        }

        return $commentsQuery->with('user')->get();
    }
}
