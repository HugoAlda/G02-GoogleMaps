<?php

namespace Database\Seeders;

use App\Models\Juego;
use Illuminate\Database\Seeder;

class JuegoSeeder extends Seeder
{
    public function run(): void
    {
        Juego::create([
            'nombre' => 'Gymkana'
        ]);
    }
}
