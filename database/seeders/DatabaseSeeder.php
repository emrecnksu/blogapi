<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Database\Seeders\PostSeeder;
use Spatie\Permission\Models\Role;
use Database\Seeders\CategorySeeder;
use Database\Seeders\AdminUserSeeder;
use Spatie\Permission\Models\Permission;
use Database\Seeders\RolePermissionSeeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
            CategorySeeder::class,
            PostSeeder::class,
        ]);
    }
}
