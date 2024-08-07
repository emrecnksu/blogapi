<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Post;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Jobs\SendCommentNotification;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class CommentController
{
    use ResponseTrait;

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

        return $this->successResponse(CommentResource::collection($comments));
    }

    public function store(CommentRequest $request)
    {
        try {
            $requestValidated = $request->validated();
            $post = Post::where('slug', $requestValidated['post_slug'])->firstOrFail();
            
            $userId = Auth::id();
            if (!$userId) {
                return $this->errorResponse('Kullanıcı oturum açmamış.', 401);
            }

            $comment = Comment::create([
                'post_id' => $post->id,
                'user_id' => $userId,
                'content' => $requestValidated['content'],
                'approved' => false,
                'approval_token' => (string) \Illuminate\Support\Str::uuid(),
            ]);

            SendCommentNotification::dispatch($comment);
            $this->clearCache($post->slug);

            return $this->successResponse(new CommentResource($comment), 'Yorum başarıyla eklendi ve admin onayı bekliyor.');
        } catch (Exception $e) {
            Log::error('Yorum ekleme hatası: ' . $e->getMessage());
            return $this->errorResponse('Yorum eklenemedi.', 500);
        }
    }

    public function update(CommentRequest $request, $id)
    {
        $requestValidated = $request->validated();
        $comment = Comment::find($id);

        if (!$comment) {
            return $this->errorResponse('Yorum bulunamadı', 404);
        }

        if ($comment->user_id !== Auth::id()) {
            return $this->errorResponse('Bu yorumu güncelleme yetkiniz yok', 403);
        }

        $comment->update([
            'content' => $requestValidated['content'],
        ]);

        $this->clearCache($comment->post->slug);

        return $this->successResponse(new CommentResource($comment), 'Yorum başarıyla güncellendi.');
    }

    public function delete($id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return $this->errorResponse('Yorum bulunamadı', 404);
        }

        if ($comment->user_id !== Auth::id() && !Auth::user()->hasRole('super-admin')) {
            return $this->errorResponse('Bu işlemi yapmak için yetkiniz yok', 403);
        }

        $slug = $comment->post->slug;
        $comment->delete();

        $this->clearCache($slug);

        return $this->successResponse(null, 'Yorum başarıyla silindi.');
    }

    public function approve(Request $request, $id)
    {
        if (!Auth::user()->hasRole('super-admin')) {
            return $this->errorResponse('Bu işlemi yapmak için yetkiniz yok', 403);
        }

        $comment = Comment::find($id);
        if (!$comment) {
            return $this->errorResponse('Yorum bulunamadı', 404);
        }

        $expectedToken = $comment->approval_token;
        if ($request->input('approval_token') !== $expectedToken) {
            return $this->errorResponse('Geçersiz token', 403);
        }

        $comment->update(['approved' => true, 'approval_token' => null]);

        $this->clearCache($comment->post->slug);

        return $this->successResponse(new CommentResource($comment), 'Yorum başarıyla onaylandı.');
    }

    private function clearCache($postId = null)
    {
        Cache::forget('comments_all');
        if ($postId) {
            Cache::forget('comments_post_'.$postId);
        }
    }
}
