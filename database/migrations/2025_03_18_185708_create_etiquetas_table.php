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
            $table->enum('icono', ['<i class="fa-solid fa-monument"></i>', '<i class="fa-solid fa-globe"></i>', '<i class="fa-solid fa-hotel"></i>', '<i class="fa-solid fa-circle-info"></i>', '<i class="fa-solid fa-futbol"></i>', '<i class="fa-solid fa-tree"></i>', '<i class="fa-solid fa-umbrella-beach"></i>','<i class="fa-solid fa-star"></i>'])->nullable();
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
