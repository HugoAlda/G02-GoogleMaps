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
        Schema::create('marcadores_etiquetas', function (Blueprint $table) {
            $table->foreignId('marcador_id')->constrained('marcadores');
            $table->foreignId('etiqueta_id')->constrained('etiquetas');
            $table->primary(['marcador_id', 'etiqueta_id']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('marcadores_etiquetas');
    }
};
