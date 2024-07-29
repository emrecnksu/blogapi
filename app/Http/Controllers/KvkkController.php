<?php

namespace App\Http\Controllers;

use App\Models\Kvkk;
use Illuminate\Http\Request;

class KvkkController
{
    /**
     * Display the KVKK content.
     */
    public function showkvkk()
    {
        $kvkk = Kvkk::first(); 
        return response()->json(['kvkk' => $kvkk]);
    }
}
