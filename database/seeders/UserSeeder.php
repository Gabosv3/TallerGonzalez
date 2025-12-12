<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Contraseña: "password123"
        $rows = [
            ['name' => 'Gabriel', 'email' => 'Gabriel@example.com', 'password' => Hash::make('password123'), 'email_verified_at' => now(), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Raul Alegria', 'email' => 'raulemi728@gmail.com', 'password' => Hash::make('Emilio2007$'), 'email_verified_at' => now(), 'created_at' => now(), 'updated_at' => now()]
        ];

        // Upsert por `email` para no duplicar usuarios
        DB::table('users')->upsert(
            $rows,
            ['email'],
            ['name','password','email_verified_at','updated_at']
        );

        // Asignar rol super_admin al usuario Gabriel
        $user = DB::table('users')->where('email', 'Gabriel@example.com')->first();
        if ($user) {
            $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
            \App\Models\User::find($user->id)->assignRole($superAdminRole);
            $this->command->info('✅ Rol super_admin asignado a Gabriel.');
        }

        $this->command->info('✅ Usuario(s) insertados/actualizados.');
    }
}
