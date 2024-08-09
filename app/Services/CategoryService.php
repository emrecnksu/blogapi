<?php

namespace App\Services;

use App\Repositories\CategoryRepository;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories()
    {
        return $this->categoryRepository->getAllCategories();
    }

    public function getCategoryBySlug(string $slug)
    {
        return $this->categoryRepository->getCategoryBySlug($slug);
    }

    public function getPostsByCategorySlug(string $slug)
    {
        return $this->categoryRepository->getCategoryWithPostsBySlug($slug);
    }
}
