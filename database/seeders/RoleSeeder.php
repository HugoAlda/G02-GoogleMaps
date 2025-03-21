<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['nombre' => 'Administrador']);
        Role::create(['nombre' => 'Cliente']);
    }
}
