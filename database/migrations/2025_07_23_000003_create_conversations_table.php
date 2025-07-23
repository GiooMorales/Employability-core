<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_one_id');
            $table->unsignedBigInteger('user_two_id');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_one_id')->references('id_usuarios')->on('usuarios')->onDelete('cascade');
            $table->foreign('user_two_id')->references('id_usuarios')->on('usuarios')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
}; 