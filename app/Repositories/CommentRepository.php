<?php

namespace App\Repositories;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CommentRepository
{
    public function getCommentsByPostSlug($postSlug = null)
    {
        $commentsQuery = Comment::where('approved', true);

        if ($postSlug) {
            $post = Post::where('slug', $postSlug)->firstOrFail();
            $commentsQuery = $commentsQuery->where('post_id', $post->id);
        }

        return $commentsQuery->with('user')->get();
    }

    public function createComment($postId, $userId, $content)
    {
        return Comment::create([
            'post_id' => $postId,
            'user_id' => $userId,
            'content' => $content,
            'approved' => false,
            'approval_token' => (string) Str::uuid(),
        ]);
    }

    public function updateComment($comment, $content)
    {
        return $comment->update([
            'content' => $content,
        ]);
    }

    public function deleteComment($comment)
    {
        return $comment->delete();
    }

    public function findCommentById($id)
    {
        return Comment::find($id);
    }

    public function approveComment($comment)
    {
        return $comment->update(['approved' => true, 'approval_token' => null]);
    }

    public function findPostBySlug($slug)
    {
        return Post::where('slug', $slug)->firstOrFail();
    }
}
