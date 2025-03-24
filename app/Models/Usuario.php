<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Importar la clase Authenticatable
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Authenticatable // Cambiar a Authenticatable
{
    use HasFactory;

    protected $table = 'usuarios'; // Tabla 'usuarios'

    protected $fillable = [
        'nombre',
        'apellidos',
        'username',
        'email',
        'password',
        'rol_id',
    ];

    protected $hidden = [
        'password', // Ocultar la contraseña
    ];

    public function rol()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    public function jugador()
    {
        return $this->hasOne(Jugador::class, 'usuario_id');
    }

    public function etiquetas()
    {
        return $this->hasMany(Etiqueta::class, 'usuario_id');
    }
}