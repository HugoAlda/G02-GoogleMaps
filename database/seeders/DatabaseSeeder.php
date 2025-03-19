<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UsuarioSeeder;
use Database\Seeders\JuegoSeeder;
use Database\Seeders\GrupoSeeder;
use Database\Seeders\MarcadorSeeder;
use Database\Seeders\EtiquetaSeeder;
use Database\Seeders\PuntoControlSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UsuarioSeeder::class,
            JuegoSeeder::class,
            GrupoSeeder::class,
            MarcadorSeeder::class,
            EtiquetaSeeder::class,
            PuntoControlSeeder::class,
        ]);
    }
}
