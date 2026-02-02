<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::table('paiements', function (Blueprint $table) {
      $table->text('validation_notes')->nullable()->after('validated_by');
    });
  }

  public function down()
  {
    Schema::table('paiements', function (Blueprint $table) {
      $table->dropColumn('validation_notes');
    });
  }
};
