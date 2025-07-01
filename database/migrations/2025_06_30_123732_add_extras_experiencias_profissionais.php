<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiencias_profissionais', function (Blueprint $table) {
            $table->string('tipo', 50)->nullable()->after('descricao');
            $table->string('modalidade', 50)->nullable()->after('tipo');
            $table->text('conquistas')->nullable()->after('modalidade');
            $table->boolean('atual')->default(0)->after('conquistas');
        });
    }

    public function down(): void
    {
        Schema::table('experiencias_profissionais', function (Blueprint $table) {
            $table->dropColumn(['tipo', 'modalidade', 'conquistas', 'atual']);
        });
    }
}; 