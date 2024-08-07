<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'category_id',
        'image',
        'status',
        'start_date',
        'end_date',
        'user_id'
    ];

    protected static function booted()
    {
        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = static::createUniqueSlug($post->title);
            }
        });

        static::updating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = static::createUniqueSlug($post->title, $post->id);
            }
        });
    }

    private static function createUniqueSlug($title, $id = 0)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (Post::where('slug', $slug)->where('id', '<>', $id)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('status', true);
    }

    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }
}
