<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::firstOrCreate(
            ['email' => 'emrecanblogsite@gmail.com'],
            [
                'name' => 'Emrecan',
                'surname' => 'Aksu',
                'password' => bcrypt('laravelmakeloveblogsite123.'),
                'is_active' => true
            ]
        );

        // Assign super-admin role //
        if (!$adminUser->hasRole('super-admin')) {
            $role = Role::firstOrCreate(['name' => 'super-admin']);
            $adminUser->assignRole($role);
        }
    }
}
