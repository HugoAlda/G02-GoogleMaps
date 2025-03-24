<?php

namespace Database\Seeders;

use App\Models\Usuario;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('nombre', 'Administrador')->first();
        $clienteRole = Role::where('nombre', 'Cliente')->first();

        // Admin user
        Usuario::create([
            'nombre' => 'Admin',
            'apellidos' => 'Sistema',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'rol_id' => $adminRole->id
        ]);

        // Cliente users (16 users for 4 groups with 4 users each)
        for ($i = 1; $i <= 16; $i++) {
            Usuario::create([
                'nombre' => "Cliente{$i}",
                'apellidos' => "Apellido{$i}",
                'username' => "cliente{$i}",
                'email' => "cliente{$i}@example.com",
                'password' => Hash::make('password123'),
                'rol_id' => $clienteRole->id
            ]);
        }
    }
}
