<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Etiqueta extends Model
{
    use HasFactory;

    protected $table = 'etiquetas';

    protected $fillable = [
        'nombre',
        'icono',
        'es_privado',
        'usuario_id',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function marcadores()
    {
        return $this->belongsToMany(Marcador::class, 'marcadores_etiquetas', 'etiqueta_id', 'marcador_id');
    }
}
