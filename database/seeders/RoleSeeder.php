<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\Permission;
class RoleSeeder extends Seeder
{
    public function run()
    {
        $admin = Role::updateOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $editor = Role::updateOrCreate(['name' => 'editor', 'guard_name' => 'api']);

        $permissions = Permission::pluck('name')->toArray();

        $admin->syncPermissions($permissions);
        $editor->syncPermissions(['view_users', 'edit_users']);
    }
}
