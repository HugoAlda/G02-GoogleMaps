<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class MarcadoresEtiquetas extends Model
{
    protected $table = 'marcadores_etiquetas';

    protected $fillable = [
        'marcador_id',
        'etiqueta_id'
    ];

    public function marcadores()
    {
        return $this->belongsToMany(Marcador::class, 'marcadores_etiquetas', 'etiqueta_id', 'marcador_id');
    }
    
    public function etiquetas()
    {
        return $this->belongsToMany(Etiqueta::class, 'marcadores_etiquetas', 'marcador_id', 'etiqueta_id');
    }

}
