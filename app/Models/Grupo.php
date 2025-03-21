<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Grupo extends Model
{
    use HasFactory;

    protected $table = 'grupos';

    protected $fillable = [
        'nombre',
        'estado',
    ];

    public function partidas()
    {
        return $this->belongsToMany(Partida::class, 'grupos_partidas', 'grupo_id', 'partida_id');
    }

    public function puntosControl()
    {
        return $this->hasMany(PuntoControl::class, 'grupo_id');
    }

    public function jugadores()
    {
        return $this->belongsToMany(Jugador::class, 'jugadores_grupos', 'grupo_id', 'jugador_id');
    }

    // Método para obtener el owner del grupo
    public function owner()
    {
        return $this->belongsTo(Jugador::class, 'jugadores_grupos', 'grupo_id', 'jugador_id')
        ->wherePivot('is_owner', true);
    }
}
