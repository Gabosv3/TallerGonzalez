<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class BackupPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'download-backup',
            'delete-backup',
            'create-backup',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Assign to a role (optional)
        $role = Role::firstOrCreate(['name' => 'super_admin']); // Usamos super_admin o el rol que prefieras
        $role->givePermissionTo($permissions);

        // Assign role to a user (optional)
        $user = User::find(1); // Cambia el ID según sea necesario

        if ($user && !$user->hasRole('super_admin')) {
            $user->assignRole('super_admin');
        }
    }
}
