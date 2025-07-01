<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formacoes', function (Blueprint $table) {
            $table->bigIncrements('id_formacoes');
            $table->unsignedBigInteger('usuario_id');
            $table->string('instituicao', 255);
            $table->string('curso', 255);
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->string('logo', 255)->nullable();
            $table->timestamps();
            $table->string('nivel', 255)->nullable();
            $table->string('situacao', 255)->nullable();
            $table->foreign('usuario_id')->references('id_usuarios')->on('usuarios')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formacoes');
    }
}; 