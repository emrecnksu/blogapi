<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $writerRole = Role::firstOrCreate(['name' => 'writer']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Permissions
        $permissions = [
            // Category Permissions
            'view_category', 'view_any_category', 'create_category', 'update_category', 'delete_category', 'delete_any_category', 'force_delete_category', 'force_delete_any_category', 'restore_category', 'restore_any_category', 'replicate_category', 'reorder_category',
            // Post Permissions
            'view_post', 'view_any_post', 'create_post', 'update_post', 'delete_post', 'delete_any_post', 'force_delete_post', 'force_delete_any_post', 'restore_post', 'restore_any_post', 'replicate_post', 'reorder_post',
            // Role Permissions
            'view_role', 'view_any_role', 'create_role', 'update_role', 'delete_role', 'delete_any_role', 'force_delete_role', 'force_delete_any_role', 'restore_role', 'restore_any_role', 'replicate_role', 'reorder_role',
            // User Permissions
            'view_user', 'view_any_user', 'create_user', 'update_user', 'delete_user', 'delete_any_user', 'force_delete_user', 'force_delete_any_user', 'restore_user', 'restore_any_user', 'replicate_user', 'reorder_user',
            // Comment Permissions 
            'view_comment', 'view_any_comment', 'create_comment', 'update_comment', 'delete_comment', 'delete_any_comment', 'force_delete_comment', 'force_delete_any_comment', 'restore_comment', 'restore_any_comment', 'replicate_comment', 'reorder_comment',
            // Tag Permissions
            'view_tag', 'view_any_tag', 'create_tag', 'update_tag', 'delete_tag', 'delete_any_tag', 'force_delete_tag', 'force_delete_any_tag', 'restore_tag', 'restore_any_tag', 'replicate_tag', 'reorder_tag',
            // TextContent Permissions
            'view_text::content', 'view_any_text::content', 'create_text::content', 'update_text::content', 'delete_text::content', 'delete_any_text::content', 'force_delete_text::content', 'force_delete_any_text::content', 'restore_text::content', 'restore_any_text::content', 'replicate_text::content', 'reorder_text::content',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to super-admin
        $superAdminRole->syncPermissions(Permission::all());

        // Assign specific permissions to writer
        $writerRole->syncPermissions([
            'view_post', 'view_any_post', 'create_post', 'update_post', 'delete_post',
        ]);

        // Assign specific permissions to user
        $userRole->syncPermissions([]);
    }
}
