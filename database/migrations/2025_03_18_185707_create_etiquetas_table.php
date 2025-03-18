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
        Schema::create('etiquetas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->string('icono', 50);
            $table->boolean('es_privado')->default(false);
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('etiquetas');
    }
};
