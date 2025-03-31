<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespuestaJugador extends Model
{
    use HasFactory;

    protected $table = 'respuestas_jugadores';

    protected $fillable = [
        'jugador_id',
        'partida_id',
        'punto_control_id',
        'respondido_en'
    ];

    // Relaciones

    public function jugador()
    {
        return $this->belongsTo(Jugador::class);
    }

    public function partida()
    {
        return $this->belongsTo(Partida::class);
    }

    public function punto()
    {
        return $this->belongsTo(PuntoControl::class, 'punto_control_id');
    }
}