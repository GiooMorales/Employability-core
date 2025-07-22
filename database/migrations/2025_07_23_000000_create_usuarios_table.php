<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->bigIncrements('id_usuarios');
            $table->string('nome', 100);
            $table->string('titulo', 100)->nullable();
            $table->text('sobre')->nullable();
            $table->string('email', 100)->unique();
            $table->string('senha', 100);
            $table->string('profissao', 100)->nullable();
            $table->text('bio')->nullable();
            $table->string('cidade', 100)->nullable();
            $table->string('estado', 100)->nullable();
            $table->string('pais', 100)->nullable();
            $table->text('url_foto')->nullable();
            $table->timestamp('data_criacao')->useCurrent();
            $table->timestamps();
            $table->string('link', 255)->nullable();
            $table->integer('quantidade_conn')->default(0);
            $table->string('github_token')->nullable();
            $table->string('github_refresh_token')->nullable();
            $table->string('github_username')->nullable();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
}; 