<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Langue;

return new class extends Migration
{
  public function up()
  {
    Schema::table('candidats', function (Blueprint $table) {
      $table->enum('premiere_langue', Langue::values())
        ->default(Langue::FRANCAIS->value)
        ->after('nationalite_cand');

      $table->string('autre_langue', 50)->nullable()->after('premiere_langue')
        ->comment('Préciser si premiere_langue = Autre');

      $table->index('premiere_langue');
    });
  }

  public function down()
  {
    Schema::table('candidats', function (Blueprint $table) {
      $table->dropIndex(['premiere_langue']);
      $table->dropColumn(['premiere_langue', 'autre_langue']);
    });
  }
};
