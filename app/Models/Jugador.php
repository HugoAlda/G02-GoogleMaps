<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jugador extends Model
{
    use HasFactory;

    protected $table = 'jugadores';

    protected $fillable = [
        'usuario_id',
        'puntos',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'jugadores_grupos', 'jugador_id', 'grupo_id');
    }
}