<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Juego extends Model
{
    use HasFactory;

    protected $table = 'juegos';

    protected $fillable = [
        'nombre',
    ];

    public function partidas()
    {
        return $this->hasMany(Partida::class, 'juego_id');
    }

    public function puntosControl()
    {
        return $this->hasMany(PuntoControl::class, 'juego_id');
    }
}
