<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasColumn('experiencias_profissionais', 'usuario_id')) {
            Schema::table('experiencias_profissionais', function (Blueprint $table) {
                $table->unsignedBigInteger('usuario_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('experiencias_profissionais', 'usuario_id')) {
            Schema::table('experiencias_profissionais', function (Blueprint $table) {
                $table->dropColumn('usuario_id');
            });
        }
    }
}; 