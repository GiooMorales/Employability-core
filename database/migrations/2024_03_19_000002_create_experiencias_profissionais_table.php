<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('experiencias_profissionais', function (Blueprint $table) {
            $table->id('id_experiencias_profissionais');
            $table->foreignId('usuario_id')->constrained('usuarios', 'id_usuarios')->onDelete('cascade');
            $table->string('cargo', 100);
            $table->string('empresa_nome', 100);
            $table->text('descricao');
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('experiencias_profissionais');
    }
}; 