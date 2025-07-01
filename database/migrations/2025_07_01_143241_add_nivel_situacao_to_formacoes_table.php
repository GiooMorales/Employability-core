<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('formacoes', function (Blueprint $table) {
            $table->string('nivel')->nullable();
            $table->string('situacao')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('formacoes', function (Blueprint $table) {
            $table->dropColumn(['nivel', 'situacao']);
        });
    }
};
