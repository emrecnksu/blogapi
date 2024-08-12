<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Jobs\SendCommentNotification;
use App\Services\CommentService;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class CommentController
{
    use ResponseTrait;

    protected $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function index(Request $request)
    {
        $postSlug = $request->query('post_slug');
        $comments = $this->commentService->getComments($postSlug);
        return $this->successResponse(CommentResource::collection($comments));
    }

    public function store(CommentRequest $request)
    {
        try {
            $requestValidated = $request->validated();
            $post = $this->commentService->findPostBySlug($requestValidated['post_slug']);

            $userId = Auth::id();
            if (!$userId) {
                return $this->errorResponse('Kullanıcı oturum açmamış.', 401);
            }

            $comment = $this->commentService->storeComment($post, $userId, $requestValidated['content']);
            SendCommentNotification::dispatch($comment);

            return $this->successResponse(new CommentResource($comment), 'Yorum başarıyla eklendi ve admin onayı bekliyor.');
        } catch (Exception $e) {
            Log::error('Yorum ekleme hatası: ' . $e->getMessage());
            return $this->errorResponse('Yorum eklenemedi.', 500);
        }
    }

    public function update(CommentRequest $request, $id)
    {
        $requestValidated = $request->validated();
        $comment = $this->commentService->findCommentById($id);

        if (!$comment) {
            return $this->errorResponse('Yorum bulunamadı', 404);
        }

        if ($comment->user_id !== Auth::id()) {
            return $this->errorResponse('Bu yorumu güncelleme yetkiniz yok', 403);
        }

        $this->commentService->updateComment($comment, $requestValidated['content']);

        return $this->successResponse(new CommentResource($comment), 'Yorum başarıyla güncellendi.');
    }

    public function delete($id)
    {
        $comment = $this->commentService->findCommentById($id);

        if (!$comment) {
            return $this->errorResponse('Yorum bulunamadı', 404);
        }

        if ($comment->user_id !== Auth::id() && !Auth::user()->hasRole('super-admin')) {
            return $this->errorResponse('Bu işlemi yapmak için yetkiniz yok', 403);
        }

        $this->commentService->deleteComment($comment);

        return $this->successResponse(null, 'Yorum başarıyla silindi.');
    }

    public function approve(Request $request, $id)
    {
        if (!Auth::user()->hasRole('super-admin')) {
            return $this->errorResponse('Bu işlemi yapmak için yetkiniz yok', 403);
        }

        $comment = $this->commentService->findCommentById($id);
        if (!$comment) {
            return $this->errorResponse('Yorum bulunamadı', 404);
        }

        $expectedToken = $comment->approval_token;
        if ($request->input('token') !== $expectedToken) {
            return $this->errorResponse('Geçersiz token', 403);
        }

        $this->commentService->approveComment($comment);

        return $this->successResponse(new CommentResource($comment), 'Yorum başarıyla onaylandı.');
    }
}
