<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'apellidos',
        'username',
        'email',
        'password',
        'rol_id',
    ];

    protected $hidden = [
        'password',
    ];

    public function rol()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'usuarios_grupos', 'usuario_id', 'grupo_id');
    }

    public function marcadores()
    {
        return $this->hasMany(Marcador::class, 'usuario_id');
    }

    public function etiquetas()
    {
        return $this->hasMany(Etiqueta::class, 'usuario_id');
    }
}
