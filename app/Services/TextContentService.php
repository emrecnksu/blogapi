<?php

namespace App\Services;

use App\Repositories\TextContentRepository;

class TextContentService
{
    protected $textContentRepository;

    public function __construct(TextContentRepository $textContentRepository)
    {
        $this->textContentRepository = $textContentRepository;
    }

    public function getTextContentByType(string $type)
    {
        return $this->textContentRepository->findByType($type);
    }
}
