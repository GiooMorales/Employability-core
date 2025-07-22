<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('experiencias_profissionais', function (Blueprint $table) {
            $table->bigIncrements('id_experiencias_profissionais');
            $table->unsignedBigInteger('usuario_id');
            $table->string('cargo', 100);
            $table->string('empresa_nome', 100);
            $table->text('descricao');
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->timestamps();
            $table->string('tipo', 50)->nullable();
            $table->string('modalidade', 50)->nullable();
            $table->text('conquistas')->nullable();
            $table->boolean('atual')->default(0);
            $table->foreign('usuario_id')->references('id_usuarios')->on('usuarios')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('experiencias_profissionais');
    }
}; 