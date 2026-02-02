<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
  /**
   * Run the migrations.
   * 
   * Cette migration ajoute un commentaire explicatif sur date_examen
   * et synchronise les dates existantes avec les plannings
   */
  public function up()
  {
    // Ajouter un commentaire sur la colonne date_examen
    if (DB::getDriverName() === 'mysql') {
      DB::statement("
                ALTER TABLE concours 
                MODIFY COLUMN date_examen DATE NULL 
                COMMENT 'Date de début des examens - Synchronisée automatiquement avec planning_epreuves'
            ");
    }

    // Synchroniser les dates existantes avec les plannings
    DB::statement("
            UPDATE concours c
            SET date_examen = (
                SELECT MIN(pe.date_epreuve)
                FROM planning_epreuves pe
                WHERE pe.concours_id = c.id
                AND pe.est_actif = true
            )
            WHERE EXISTS (
                SELECT 1 
                FROM planning_epreuves pe 
                WHERE pe.concours_id = c.id 
                AND pe.est_actif = true
            )
        ");

    $count = DB::table('concours')
      ->whereNotNull('date_examen')
      ->count();

    \Log::info("Synchronisation date_examen: {$count} concours mis à jour");
  }

  /**
   * Reverse the migrations.
   */
  public function down()
  {
    // Retirer le commentaire
    if (DB::getDriverName() === 'mysql') {
      DB::statement("
                ALTER TABLE concours 
                MODIFY COLUMN date_examen DATE NULL
            ");
    }
  }
};
