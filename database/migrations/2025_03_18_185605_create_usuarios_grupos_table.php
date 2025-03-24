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
        Schema::create('usuarios_grupos', function (Blueprint $table) {
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->foreignId('grupo_id')->constrained('grupos');
            $table->primary(['usuario_id', 'grupo_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarios_grupos');
    }
};
