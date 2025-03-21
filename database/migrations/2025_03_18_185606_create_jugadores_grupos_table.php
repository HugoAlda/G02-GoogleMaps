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
        Schema::create('jugadores_grupos', function (Blueprint $table) {
            $table->foreignId('jugador_id')->constrained('jugadores');
            $table->foreignId('grupo_id')->constrained('grupos');
            $table->primary(['jugador_id', 'grupo_id']);
            $table->boolean('is_owner')->default(false);
        });
    }

    public function down()
    {
        Schema::dropIfExists('jugadores_grupos');
    }
};
