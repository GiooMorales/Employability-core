<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('experiencias_profissionais', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('id_usuario');
            $table->string('cargo', 100);
            $table->string('empresa', 100);
            $table->string('localizacao', 100)->nullable();
            $table->text('descricao')->nullable();
            $table->date('inicio');
            $table->date('fim')->nullable();
            $table->boolean('atual')->default(false);
            $table->string('tipo_contrato', 50)->nullable();
            $table->string('setor', 50)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->foreign('id_usuario')->references('id_usuarios')->on('usuarios')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('experiencias_profissionais');
    }
}; 