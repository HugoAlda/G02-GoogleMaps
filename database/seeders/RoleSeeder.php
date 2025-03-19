<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['nombre' => 'admin']);
        Role::create(['nombre' => 'cliente']);
    }
}
