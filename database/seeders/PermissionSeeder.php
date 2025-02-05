<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            ['name' => 'view_users', 'module_name' => 'User Management', 'sub_module_name' => 'Users', 'display_name' => 'View Users'],
            ['name' => 'edit_users', 'module_name' => 'User Management', 'sub_module_name' => 'Users', 'display_name' => 'Edit Users'],
            ['name' => 'delete_users', 'module_name' => 'User Management', 'sub_module_name' => 'Users', 'display_name' => 'Delete Users'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name'], 'guard_name' => 'api'],
                $permission
            );
        }
    }
}
