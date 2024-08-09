<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Services\CommentService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;
use App\Jobs\SendCommentNotification;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\CommentResource;

class CommentController
{
    use ResponseTrait;

    private $commentService;
    private $cacheDuration = 60;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function index(Request $request)
    {
        $postSlug = $request->query('post_slug');
        $cacheKey = $postSlug ? 'comments_post_' . $postSlug : 'comments_all';

        $comments = Cache::remember($cacheKey, $this->cacheDuration, function () use ($postSlug) {
            return $this->commentService->getCommentsByPostSlug($postSlug);
        });

        return $this->successResponse(CommentResource::collection($comments));
    }

    public function store(CommentRequest $request)
    {
        try {
            $requestValidated = $request->validated();
            $userId = Auth::id();
            if (!$userId) {
                return $this->errorResponse('Kullanıcı oturum açmamış.', 401);
            }

            $comment = $this->commentService->createComment([
                'post_id' => Post::where('slug', $requestValidated['post_slug'])->firstOrFail()->id,
                'user_id' => $userId,
                'content' => $requestValidated['content'],
                'approved' => false,
                'approval_token' => (string)\Illuminate\Support\Str::uuid(),
            ]);

            SendCommentNotification::dispatch($comment);
            $this->clearCache($comment->post->slug);

            return $this->successResponse(new CommentResource($comment), 'Yorum başarıyla eklendi ve admin onayı bekliyor.');
        } catch (Exception $e) {
            Log::error('Yorum ekleme hatası: ' . $e->getMessage());
            return $this->errorResponse('Yorum eklenemedi.', 500);
        }
    }

    public function update(CommentRequest $request, $id)
    {
        $comment = $this->commentService->findById($id);

        if (!$comment) {
            return $this->errorResponse('Yorum bulunamadı', 404);
        }

        if ($comment->user_id !== Auth::id()) {
            return $this->errorResponse('Bu yorumu güncelleme yetkiniz yok', 403);
        }

        $this->commentService->updateComment($comment, $request->validated());
        $this->clearCache($comment->post->slug);

        return $this->successResponse(new CommentResource($comment), 'Yorum başarıyla güncellendi.');
    }

    public function delete($id)
    {
        $comment = $this->commentService->findById($id);

        if (!$comment) {
            return $this->errorResponse('Yorum bulunamadı', 404);
        }

        if ($comment->user_id !== Auth::id() && !Auth::user()->hasRole('super-admin')) {
            return $this->errorResponse('Bu işlemi yapmak için yetkiniz yok', 403);
        }

        $this->commentService->deleteComment($comment);
        $this->clearCache($comment->post->slug);

        return $this->successResponse(null, 'Yorum başarıyla silindi.');
    }

    public function approve(Request $request, $id)
    {
        if (!Auth::user()->hasRole('super-admin')) {
            return $this->errorResponse('Bu işlemi yapmak için yetkiniz yok', 403);
        }

        $comment = $this->commentService->findById($id);
        if (!$comment) {
            return $this->errorResponse('Yorum bulunamadı', 404);
        }

        $token = $request->input('approval_token');
        if ($this->commentService->approveComment($comment, $token)) {
            $this->clearCache($comment->post->slug);
            return $this->successResponse(new CommentResource($comment), 'Yorum başarıyla onaylandı.');
        }

        return $this->errorResponse('Geçersiz token', 403);
    }

    private function clearCache($postId = null)
    {
        Cache::forget('comments_all');
        if ($postId) {
            Cache::forget('comments_post_' . $postId);
        }
    }
}
