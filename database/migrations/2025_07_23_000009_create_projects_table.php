<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('titulo', 255);
            $table->text('descricao');
            $table->string('url', 255)->nullable();
            $table->string('imagem', 255)->nullable();
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id_usuarios')->on('usuarios')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
}; 