<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;
use App\Jobs\SendCommentNotification;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\CommentResource;

class CommentController
{
    private $cacheDuration = 60;

    public function index(Request $request)
    {
        $postId = $request->query('post_id');
        $cacheKey = $postId ? 'comments_post_'.$postId : 'comments_all';

        $comments = Cache::remember($cacheKey, $this->cacheDuration, function() use ($postId) {
            $commentsQuery = Comment::where('approved', true);

            if ($postId) {
                $commentsQuery = $commentsQuery->where('post_id', $postId);
            }

            return $commentsQuery->with('user')->get();
        });

        return CommentResource::collection($comments);
    }

    public function store(CommentRequest $request)
    {
        $validated = $request->validated();

        $userId = $request->user('sanctum')->id;

        $comment = Comment::create([
            'post_id' => $validated['post_id'],
            'user_id' => $userId,
            'content' => $validated['content'],
            'approved' => false,
        ]);

        SendCommentNotification::dispatch($comment);

        $this->clearCache($validated['post_id']);

        return (new CommentResource($comment))->additional(['message' => 'Yorum başarıyla eklendi ve admin onayı bekliyor.']);
    }

    public function update(CommentRequest $request, $id)
    {
        $validated = $request->validated();
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['status' => 0, 'message' => 'Yorum bulunamadı'], 404);
        }

        if ($comment->user_id !== Auth::id()) {
            return response()->json(['status' => 0, 'message' => 'Bu yorumu güncelleme yetkiniz yok'], 403);
        }

        $comment->update([
            'content' => $validated['content'],
        ]);

        $this->clearCache($comment->post_id);

        return (new CommentResource($comment))->additional(['message' => 'Yorum başarıyla güncellendi.']);
    }

    public function delete($id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['status' => 0, 'message' => 'Yorum bulunamadı'], 404);
        }

        if ($comment->user_id !== Auth::id() && !Auth::user()->hasRole('super-admin')) {
            return response()->json(['status' => 0, 'message' => 'Bu işlemi yapmak için yetkiniz yok'], 403);
        }

        $comment->delete();

        $this->clearCache($comment->post_id);

        return response()->json(['status' => 1, 'message' => 'Yorum başarıyla silindi.'], 200);
    }

    public function approve($id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['status' => 0, 'message' => 'Yorum bulunamadı'], 404);
        }

        if (!Auth::user()->hasRole('super-admin')) {
            return response()->json(['status' => 0, 'message' => 'Bu işlemi yapmak için yetkiniz yok'], 403);
        }

        $comment->update(['approved' => true]);

        $this->clearCache($comment->post_id);

        return (new CommentResource($comment))->additional(['message' => 'Yorum başarıyla onaylandı.']);
    }

    private function clearCache($postId = null)
    {
        Cache::forget('comments_all');
        if ($postId) {
            Cache::forget('comments_post_'.$postId);
        }
    }
}
