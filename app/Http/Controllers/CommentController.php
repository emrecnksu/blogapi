<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendCommentNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

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

        return response()->json([
            'status' => 1,
            'comments' => $comments
        ], 200);
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 0, 'message' => 'Yorum yapabilmek için giriş yapmalısınız.'], 401);
        }

        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string',
        ]);

        $comment = Comment::create([
            'post_id' => $request->post_id,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'approved' => false,
        ]);

        SendCommentNotification::dispatch($comment);

        // Clear cache after successfully adding comment //
        $this->clearCache($request->post_id);

        return response()->json([
            'status' => 1,
            'message' => 'Yorum başarıyla eklendi ve admin onayı bekliyor.',
            'comment' => $comment
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['status' => 0, 'message' => 'Yorum bulunamadı'], 404);
        }

        if ($comment->user_id !== Auth::id()) {
            return response()->json(['status' => 0, 'message' => 'Bu yorumu güncelleme yetkiniz yok'], 403);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()], 400);
        }

        $comment->update([
            'content' => $request->content,
        ]);

        $this->clearCache($comment->post_id);

        return response()->json(['status' => 1, 'message' => 'Yorum başarıyla güncellendi', 'comment' => $comment], 200);
    }

    public function delete(Request $request, $id)
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

        return response()->json(['status' => 1, 'message' => 'Yorum başarıyla silindi'], 200);
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

        return response()->json(['status' => 1, 'message' => 'Yorum başarıyla onaylandı', 'comment' => $comment], 200);
    }

    private function clearCache($postId = null)
    {
        Cache::forget('comments_all');
        if ($postId) {
            Cache::forget('comments_post_'.$postId);
        }
    }
}
