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
        Schema::table('experiencias_profissionais', function (Blueprint $table) {
            $table->string('tipo', 50)->nullable();
            $table->string('modalidade', 50)->nullable();
            $table->text('conquistas')->nullable();
            $table->boolean('atual')->default(false);
        });
    }
    
    public function down(): void
    {
        Schema::table('experiencias_profissionais', function (Blueprint $table) {
            $table->dropColumn(['tipo', 'modalidade', 'conquistas', 'atual']);
        });
    }
};