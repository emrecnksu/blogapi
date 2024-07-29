<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = [
            ['name' => 'Teknoloji', 'status' => 1],
            ['name' => 'Sağlık', 'status' => 1],
            ['name' => 'Eğitim', 'status' => 1],
            ['name' => 'Seyahat', 'status' => 1],
            ['name' => 'Spor', 'status' => 1], 
            ['name' => 'Yiyecek', 'status' => 1],
            ['name' => 'Yaşam Tarzı', 'status' => 1],
            ['name' => 'İş', 'status' => 1],
            ['name' => 'Eğlence', 'status' => 1],
            ['name' => 'Bilim', 'status' => 1],
        ];

        foreach ($categories as $category) {
            $category['slug'] = Str::slug($category['name']);
            Category::create($category);
        }
    }
}