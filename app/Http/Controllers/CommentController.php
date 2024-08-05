<?php

namespace App\Http\Controllers;

use App\Models\Post;
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
        $postSlug = $request->query('post_slug');
        $cacheKey = $postSlug ? 'comments_post_'.$postSlug : 'comments_all';

        $comments = Cache::remember($cacheKey, $this->cacheDuration, function() use ($postSlug) {
            $commentsQuery = Comment::where('approved', true);

            if ($postSlug) {
                $post = Post::where('slug', $postSlug)->firstOrFail();
                $commentsQuery = $commentsQuery->where('post_id', $post->id);
            }

            return $commentsQuery->with('user')->get();
        });

        return CommentResource::collection($comments);
    }

    public function store(CommentRequest $request)
    {
        $validated = $request->validated();

        $post = Post::where('slug', $validated['post_slug'])->firstOrFail();
        $userId = $request->user('sanctum')->id;

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $userId,
            'content' => $validated['content'],
            'approved' => false,
            'approval_token' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        SendCommentNotification::dispatch($comment);

        $this->clearCache($post->slug);

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

        $this->clearCache($comment->post->slug);

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

        $slug = $comment->post->slug;
        $comment->delete();

        $this->clearCache($slug);

        return response()->json(['status' => 1, 'message' => 'Yorum başarıyla silindi.'], 200);
    }

    public function approve(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->hasRole('super-admin')) {
            return response()->json(['status' => 0, 'message' => 'Bu işlemi yapmak için yetkiniz yok'], 403);
        }

        $comment = Comment::find($id);
        if (!$comment) {
            return response()->json(['status' => 0, 'message' => 'Yorum bulunamadı'], 404);
        }

        $expectedToken = $comment->approval_token;
        if ($request->input('token') !== $expectedToken) {
            return response()->json(['status' => 0, 'message' => 'Geçersiz token'], 403);
        }

        $comment->update(['approved' => true, 'approval_token' => null]);

        $this->clearCache($comment->post->slug);

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
