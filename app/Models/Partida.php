<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Partida extends Model
{
    use HasFactory;

    protected $table = 'partidas';

    protected $fillable = [
        'juego_id',
        'estado',
        'fecha_inicio',
        'fecha_fin',
    ];

    public function juego()
    {
        return $this->belongsTo(Juego::class, 'juego_id');
    }

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'grupos_partidas', 'partida_id', 'grupo_id');
    }
}
