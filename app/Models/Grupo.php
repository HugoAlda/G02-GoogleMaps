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

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'usuarios_grupos', 'grupo_id', 'usuario_id');
    }

    public function partidas()
    {
        return $this->belongsToMany(Partida::class, 'grupos_partidas', 'grupo_id', 'partida_id');
    }

    public function puntosControl()
    {
        return $this->hasMany(PuntoControl::class, 'grupo_id');
    }
}
