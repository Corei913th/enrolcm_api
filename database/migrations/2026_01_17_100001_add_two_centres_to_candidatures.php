<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
  public function up()
  {
    Schema::table('candidatures', function (Blueprint $table) {
      // Ajouter centre_depot_id AVANT de renommer
      $table->uuid('centre_depot_id')->nullable()->after('centre_id');
      $table->foreign('centre_depot_id')
        ->references('id')
        ->on('centres')
        ->onDelete('set null');
      $table->index('centre_depot_id');
    });

    // Migrer les données existantes: copier centre_id vers centre_depot_id
    DB::statement('
            UPDATE candidatures 
            SET centre_depot_id = centre_id
            WHERE centre_id IS NOT NULL
        ');

    // Renommer centre_id en centre_examen_id
    Schema::table('candidatures', function (Blueprint $table) {
      $table->renameColumn('centre_id', 'centre_examen_id');
    });

    // Log pour traçabilité
    $count = DB::table('candidatures')
      ->whereNotNull('centre_examen_id')
      ->count();

    \Log::info("Migration centres: {$count} candidatures migrées avec deux centres");
  }

  public function down()
  {
    Schema::table('candidatures', function (Blueprint $table) {
      // Renommer centre_examen_id en centre_id
      $table->renameColumn('centre_examen_id', 'centre_id');

      // Supprimer centre_depot_id
      $table->dropForeign(['centre_depot_id']);
      $table->dropIndex(['centre_depot_id']);
      $table->dropColumn('centre_depot_id');
    });
  }
};
