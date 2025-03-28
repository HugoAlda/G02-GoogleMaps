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
        Schema::create('marcadores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->decimal('latitud', 10, 8);
            $table->decimal('longitud', 11, 8);
            $table->string('direccion', 300);
            $table->text('descripcion');
            $table->string('imagen', 255)->nullable();
            $table->enum('icono', ['fa-solid fa-monument', 'fa-solid fa-globe', 'fa-solid fa-hotel', 'fa-solid fa-circle-info', 'fa-solid fa-futbol', 'fa-solid fa-tree', 'fa-solid fa-umbrella-beach','fa-solid fa-star'])->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('marcadores');
    }
};
