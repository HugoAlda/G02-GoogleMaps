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
            $table->enum('icono', ['fa-solid fa-monument', 'fa-solid fa-globe', 'fa-solid fa-hotel', 'fa-solid fa-circle-info', 'fa-solid fa-futbol', 'fa-solid fa-tree', 'fa-solid fa-umbrella-beach','fa-solid fa-star'])->nullable();
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
