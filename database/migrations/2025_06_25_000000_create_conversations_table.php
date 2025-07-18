<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_one_id');
            $table->unsignedBigInteger('user_two_id');
            $table->timestamps();

            $table->foreign('user_one_id')->references('id_usuarios')->on('usuarios')->onDelete('cascade');
            $table->foreign('user_two_id')->references('id_usuarios')->on('usuarios')->onDelete('cascade');
            $table->unique(['user_one_id', 'user_two_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('conversations');
    }
}; 