<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasColumn('formacoes', 'usuario_id')) {
            Schema::table('formacoes', function (Blueprint $table) {
                $table->unsignedBigInteger('usuario_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('formacoes', 'usuario_id')) {
            Schema::table('formacoes', function (Blueprint $table) {
                $table->dropColumn('usuario_id');
            });
        }
    }
}; 