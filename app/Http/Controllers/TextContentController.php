<?php

namespace App\Http\Controllers;

use App\Services\TextContentService;
use App\Traits\ResponseTrait;

class TextContentController
{
    use ResponseTrait;

    protected $textContentService;

    public function __construct(TextContentService $textContentService)
    {
        $this->textContentService = $textContentService;
    }

    public function show($type)
    {
        $textContent = $this->textContentService->getTextContentByType($type);

        if (!$textContent) {
            return $this->errorResponse('Metin içeriği bulunamadı', 404);
        }

        return $this->successResponse($textContent);
    }
}
