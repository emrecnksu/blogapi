<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use App\Traits\ResponseTrait;

class CategoryController
{
    use ResponseTrait;

    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->getAllCategories();

        return $this->successResponse(CategoryResource::collection($categories));
    }

    public function show($slug)
    {
        $category = $this->categoryService->getCategoryBySlug($slug);

        if (!$category) {
            return $this->errorResponse('Kategori bulunamadı', 404);
        }

        return $this->successResponse(new CategoryResource($category));
    }

    public function posts($slug)
    {
        $result = $this->categoryService->getPostsByCategorySlug($slug);

        return $this->successResponse([
            'category' => new CategoryResource($result),
            'posts' => PostResource::collection($result->posts),
        ]);
    }
}
