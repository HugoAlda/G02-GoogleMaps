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
        Schema::create('grupos_partidas', function (Blueprint $table) {
            $table->foreignId('grupo_id')->constrained('grupos');
            $table->foreignId('partida_id')->constrained('partidas');
            $table->primary(['grupo_id', 'partida_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('grupos_partidas');
    }
};
