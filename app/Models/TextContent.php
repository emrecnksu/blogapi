<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TextContent extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'type',
        'text_content',
    ];
}
