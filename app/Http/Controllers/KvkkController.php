<?php

namespace App\Http\Controllers;

use App\Models\Kvkk;
use Illuminate\Http\Request;

class KvkkController
{
    /**
     * Display the KVKK content.
     */
    public function show()
    {
        $kvkk = Kvkk::latest()->first();
        return response()->json(['kvkk' => $kvkk]);
    }
}
