<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('usuarios', 'id_usuarios')->onDelete('cascade');
            $table->foreignId('connected_user_id')->constrained('usuarios', 'id_usuarios')->onDelete('cascade');
            $table->enum('status', ['pendente', 'aceita', 'recusada'])->default('pendente');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('connections');
    }
}; 