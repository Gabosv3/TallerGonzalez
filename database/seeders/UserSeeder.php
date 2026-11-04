<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Las contraseñas se generan al azar y se muestran una sola vez al ejecutar el seeder
        $passwords = [
            'Gabriel@example.com' => Str::random(20),
            'raulemi728@gmail.com' => Str::random(20),
        ];

        $rows = [
            ['name' => 'Gabriel', 'email' => 'Gabriel@example.com', 'password' => Hash::make($passwords['Gabriel@example.com']), 'email_verified_at' => now(), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Raul Alegria', 'email' => 'raulemi728@gmail.com', 'password' => Hash::make($passwords['raulemi728@gmail.com']), 'email_verified_at' => now(), 'created_at' => now(), 'updated_at' => now()]
        ];

        // Upsert por `email` para no duplicar usuarios
        DB::table('users')->upsert(
            $rows,
            ['email'],
            ['name','email_verified_at','updated_at']
        );

        // Asignar rol super_admin al usuario Gabriel
        $user = DB::table('users')->where('email', 'Gabriel@example.com')->first();
        if ($user) {
            $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
            \App\Models\User::find($user->id)->assignRole($superAdminRole);
            $this->command->info('✅ Rol super_admin asignado a Gabriel.');
        }

        $this->command->info('✅ Usuario(s) insertados/actualizados. Los usuarios existentes conservan su contraseña.');
        foreach ($passwords as $email => $password) {
            $this->command->warn("{$email}: {$password} (solo vale para usuarios nuevos)");
        }
    }
}
