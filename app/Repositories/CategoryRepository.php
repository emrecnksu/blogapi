<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    public function getAllCategories()
    {
        return Category::all();
    }

    public function getCategoryBySlug(string $slug)
    {
        return Category::bySlug($slug)->first();
    }

    public function getCategoryWithPostsBySlug(string $slug)
    {
        return Category::with(['posts' => function ($query) {
            $query->visible()->with('user', 'tags');
        }])->bySlug($slug)->firstOrFail();
    }
}
