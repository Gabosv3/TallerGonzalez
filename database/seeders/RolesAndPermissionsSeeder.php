<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['admin', 'cliente', 'vendedor'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Example: create some basic permissions if desired
        $perms = ['manage facturas', 'view productos'];
        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        // Assign example permissions to admin
        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $admin->givePermissionTo($perms);
        }
    }
}
