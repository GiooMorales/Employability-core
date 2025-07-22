<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
   {
       Schema::table('usuarios', function (Blueprint $table) {
           $table->string('github_token')->nullable();
           $table->string('github_refresh_token')->nullable();
           $table->string('github_username')->nullable();
       });
   }

   public function down()
   {
       Schema::table('usuarios', function (Blueprint $table) {
           $table->dropColumn(['github_token', 'github_refresh_token', 'github_username']);
       });
   }
};
