<?php

namespace App\Repositories;

use App\Models\TextContent;

class TextContentRepository
{
    public function findByType(string $type): ?TextContent
    {
        return TextContent::where('type', $type)->first();
    }
}
