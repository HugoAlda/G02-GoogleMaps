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
            // $table->foreignId('usuario_id')->constrained('usuarios');
            $table->decimal('latitud', 10, 8);
            $table->decimal('longitud', 11, 8);
            $table->string('direccion', 100);
            $table->text('descripcion');
            $table->string('imagen', 255)->nullable();
            $table->string('color', 20)->nullable();
            $table->string('icono', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('marcadores');
    }
};
