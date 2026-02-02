<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
  /**
   * Associer les concours existants à leurs écoles via leurs filières
   */
  public function up()
  {
    // Pour chaque concours, trouver l'école via ses filières
    $concours = DB::table('concours')->whereNull('ecole_id')->get();

    foreach ($concours as $c) {
      // Récupérer la première filière du concours
      $filiere = DB::table('concours_filiere')
        ->where('concours_id', $c->id)
        ->first();

      if ($filiere) {
        // Récupérer le département de la filière
        $departement = DB::table('filieres')
          ->join('departements', 'filieres.departement_id', '=', 'departements.id')
          ->where('filieres.id', $filiere->filiere_id)
          ->select('departements.ecole_id')
          ->first();

        if ($departement && $departement->ecole_id) {
          // Mettre à jour le concours avec l'ecole_id
          DB::table('concours')
            ->where('id', $c->id)
            ->update(['ecole_id' => $departement->ecole_id]);
        }
      }
    }
  }

  /**
   * Retirer l'association école des concours
   */
  public function down()
  {
    DB::table('concours')->update(['ecole_id' => null]);
  }
};
