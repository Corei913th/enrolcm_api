<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::table('concours', function (Blueprint $table) {
      $table->uuid('ecole_id')->nullable()->after('id');

      $table->foreign('ecole_id')
        ->references('id')
        ->on('ecoles')
        ->onDelete('restrict');

      $table->index('ecole_id');
    });
  }

  public function down()
  {
    Schema::table('concours', function (Blueprint $table) {
      $table->dropForeign(['ecole_id']);
      $table->dropColumn('ecole_id');
    });
  }
};
