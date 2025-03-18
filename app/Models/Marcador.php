<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Marcador extends Model
{
    use HasFactory;

    protected $table = 'marcadores';

    protected $fillable = [
        'nombre',
        'usuario_id',
        'latitud',
        'longitud',
        'direccion',
        'descripcion',
        'imagen',
        'color',
        'icono',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function etiquetas()
    {
        return $this->belongsToMany(Etiqueta::class, 'marcadores_etiquetas', 'marcador_id', 'etiqueta_id');
    }
}
