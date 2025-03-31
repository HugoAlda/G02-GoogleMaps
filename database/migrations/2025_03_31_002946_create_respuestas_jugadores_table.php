<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('respuestas_jugadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jugador_id')->constrained('jugadores')->onDelete('cascade');
            $table->foreignId('partida_id')->constrained('partidas')->onDelete('cascade');
            $table->foreignId('punto_control_id')->constrained('puntos_control')->onDelete('cascade');
            $table->timestamp('respondido_en')->nullable();
            $table->timestamps();

            $table->unique(['jugador_id', 'partida_id', 'punto_control_id'], 'respuesta_unica');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('respuestas_jugadores');
    }
};