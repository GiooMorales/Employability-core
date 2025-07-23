<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('connections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('connected_user_id');
            $table->enum('status', ['pendente', 'aceita', 'recusada'])->default('pendente');
            $table->timestamps();
            $table->foreign('user_id')->references('id_usuarios')->on('usuarios')->onDelete('cascade');
            $table->foreign('connected_user_id')->references('id_usuarios')->on('usuarios')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('connections');
    }
}; 