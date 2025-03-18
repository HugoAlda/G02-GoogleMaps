<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PuntoControl extends Model
{
    use HasFactory;

    protected $table = 'puntos_control';

    protected $fillable = [
        'nombre',
        'juego_id',
        'latitud',
        'longitud',
        'direccion',
        'acertijo',
        'respuesta',
        'imagen',
        'color',
        'icono',
        'grupo_id',
        'estado',
    ];

    public function juego()
    {
        return $this->belongsTo(Juego::class, 'juego_id');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }
}
