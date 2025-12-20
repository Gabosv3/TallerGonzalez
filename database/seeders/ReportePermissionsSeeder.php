<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ReportePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Crear permisos para Reportes
        $permissions = [
            'view_reporte',
            'view_any_reporte',
            'view_reporte_facturas',
            'view_any_reporte_facturas',
            'view_reporte_ventas',
            'view_any_reporte_ventas',
            'view_reporte_clientes',
            'view_any_reporte_clientes',
            'view_reporte_productos',
            'view_any_reporte_productos',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Asignar permisos al rol super_admin
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
            $this->command->info('✅ Permisos de Reportes asignados al rol super_admin.');
        }

        $this->command->info('✅ Permisos de Reportes creados correctamente.');
    }
}
