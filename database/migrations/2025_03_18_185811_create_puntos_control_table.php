<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('puntos_control', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->foreignId('juego_id')->constrained('juegos');
            $table->decimal('latitud', 10, 8);
            $table->decimal('longitud', 11, 8);
            $table->string('direccion', 100);
            $table->text('acertijo');
            $table->text('respuesta');
            $table->string('imagen', 255)->nullable();
            $table->string('color', 20)->nullable();
            $table->string('icono', 50)->nullable();
            $table->foreignId('grupo_id')->nullable()->constrained('grupos');
            $table->enum('estado', ['pendiente', 'en_curso', 'completado'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('puntos_control');
    }
};
