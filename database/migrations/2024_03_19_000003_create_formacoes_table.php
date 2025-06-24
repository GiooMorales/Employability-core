<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('formacoes', function (Blueprint $table) {
            $table->id('id_formacoes');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuarios')->onDelete('cascade');
            $table->string('instituicao');
            $table->string('curso');
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('formacoes');
    }
}; 