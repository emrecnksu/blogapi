<?php

namespace App\Http\Controllers;

use App\Models\Textcontent;
use Illuminate\Http\Request;

class TextContentController
{
    public function show($type)
    {
        $textContent = Textcontent::where('type', $type)->firstOrFail();
        return response()->json($textContent);
    }
}
