<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('usuarios', 'id_usuarios')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descricao');
            $table->string('url')->nullable();
            $table->string('imagem')->nullable();
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
}; 