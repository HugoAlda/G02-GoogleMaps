<?php

namespace Database\Seeders;

use App\Models\Grupo;
use App\Models\Usuario;
use App\Models\Juego;
use App\Models\Role;
use Illuminate\Database\Seeder;

class GrupoSeeder extends Seeder
{
    public function run(): void
    {
        $juego = Juego::where('nombre', 'Gymkana')->first();
        $clienteRole = Role::where('nombre', 'cliente')->first();
        $clientes = Usuario::where('rol_id', $clienteRole->id)->get();
        
        // Create 4 groups
        for ($i = 1; $i <= 4; $i++) {
            $grupo = Grupo::create([
                'nombre' => "Grupo {$i}",
                'estado' => 'Abierto'
            ]);

            // Assign 4 users to each group using pivot table
            $grupoClientes = $clientes->slice(($i-1)*4, 4);
            foreach($grupoClientes as $cliente) {
                \DB::table('usuarios_grupos')->insert([
                    'usuario_id' => $cliente->id,
                    'grupo_id' => $grupo->id
                ]);
            }
        }
    }
}
