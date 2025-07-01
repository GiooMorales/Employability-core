<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formacoes', function (Blueprint $table) {
            $table->string('nivel', 255)->nullable()->after('logo');
            $table->string('situacao', 255)->nullable()->after('nivel');
        });
    }

    public function down(): void
    {
        Schema::table('formacoes', function (Blueprint $table) {
            $table->dropColumn(['nivel', 'situacao']);
        });
    }
}; 