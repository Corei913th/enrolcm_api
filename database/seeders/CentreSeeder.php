<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Centre;
use App\Enums\TypeCentre;
use Faker\Factory as Faker;

class CentreSeeder extends Seeder
{
  public function run(): void
  {
    $faker = Faker::create('fr_FR');

    $this->command->info("🏫 Génération des centres d'examen...");

    // Récupérer les régions existantes
    $regions = \App\Models\Region::all();
    if ($regions->isEmpty()) {
      $this->command->error("❌ Aucune région trouvée! Veuillez d'abord exécuter le RegionSeeder.");
      return;
    }

    // Mapper les villes aux régions
    $villeRegionMap = [
      'Yaoundé' => 'CENTRE',
      'Douala' => 'LITTORAL',
      'Bafoussam' => 'OUEST',
      'Dschang' => 'OUEST',
      'Bamenda' => 'NORD_OUEST',
      'Buea' => 'SUD_OUEST',
      'Garoua' => 'NORD',
      'Maroua' => 'EXTREME_NORD',
      'Ngaoundéré' => 'ADAMAOUA',
      'Bertoua' => 'EST',
      'Ebolowa' => 'SUD',
    ];

    $centres = [
      // Centres principaux par ville
      ['nom' => 'Centre d\'Examen de Yaoundé I', 'ville' => 'Yaoundé', 'type' => TypeCentre::EXAMEN],
      ['nom' => 'Centre d\'Examen de Yaoundé II', 'ville' => 'Yaoundé', 'type' => TypeCentre::EXAMEN],
      ['nom' => 'Lycée Général Leclerc', 'ville' => 'Yaoundé', 'type' => TypeCentre::EXAMEN],
      ['nom' => 'Lycée de Ngoa-Ekelle', 'ville' => 'Yaoundé', 'type' => TypeCentre::EXAMEN],

      ['nom' => 'Centre d\'Examen de Douala I', 'ville' => 'Douala', 'type' => TypeCentre::EXAMEN],
      ['nom' => 'Centre d\'Examen de Douala II', 'ville' => 'Douala', 'type' => TypeCentre::EXAMEN],
      ['nom' => 'Lycée de Deido', 'ville' => 'Douala', 'type' => TypeCentre::EXAMEN],
      ['nom' => 'Lycée de New-Bell', 'ville' => 'Douala', 'type' => TypeCentre::EXAMEN],

      ['nom' => 'Centre d\'Examen de Bafoussam', 'ville' => 'Bafoussam', 'type' => TypeCentre::EXAMEN],
      ['nom' => 'Lycée Classique de Bafoussam', 'ville' => 'Bafoussam', 'type' => TypeCentre::EXAMEN],
      ['nom' => 'Centre d\'Examen de Dschang', 'ville' => 'Dschang', 'type' => TypeCentre::EXAMEN],

      ['nom' => 'Centre d\'Examen de Bamenda', 'ville' => 'Bamenda', 'type' => TypeCentre::EXAMEN],
      ['nom' => 'Government Bilingual High School Bamenda', 'ville' => 'Bamenda', 'type' => TypeCentre::EXAMEN],

      ['nom' => 'Centre d\'Examen de Buea', 'ville' => 'Buea', 'type' => TypeCentre::EXAMEN],
      ['nom' => 'Government Bilingual High School Buea', 'ville' => 'Buea', 'type' => TypeCentre::EXAMEN],

      ['nom' => 'Centre d\'Examen de Garoua', 'ville' => 'Garoua', 'type' => TypeCentre::EXAMEN],
      ['nom' => 'Lycée de Garoua', 'ville' => 'Garoua', 'type' => TypeCentre::EXAMEN],

      ['nom' => 'Centre d\'Examen de Maroua', 'ville' => 'Maroua', 'type' => TypeCentre::EXAMEN],
      ['nom' => 'Lycée de Maroua', 'ville' => 'Maroua', 'type' => TypeCentre::EXAMEN],

      ['nom' => 'Centre d\'Examen de Ngaoundéré', 'ville' => 'Ngaoundéré', 'type' => TypeCentre::EXAMEN],
      ['nom' => 'Lycée de Ngaoundéré', 'ville' => 'Ngaoundéré', 'type' => TypeCentre::EXAMEN],

      ['nom' => 'Centre d\'Examen de Bertoua', 'ville' => 'Bertoua', 'type' => TypeCentre::EXAMEN],
      ['nom' => 'Lycée de Bertoua', 'ville' => 'Bertoua', 'type' => TypeCentre::EXAMEN],

      ['nom' => 'Centre d\'Examen d\'Ebolowa', 'ville' => 'Ebolowa', 'type' => TypeCentre::EXAMEN],
      ['nom' => 'Lycée d\'Ebolowa', 'ville' => 'Ebolowa', 'type' => TypeCentre::EXAMEN],

      // Quelques centres de dépôt
      ['nom' => 'Centre de Dépôt Yaoundé', 'ville' => 'Yaoundé', 'type' => TypeCentre::DEPOT],
      ['nom' => 'Centre de Dépôt Douala', 'ville' => 'Douala', 'type' => TypeCentre::DEPOT],
    ];

    foreach ($centres as $centreData) {
      // Trouver la région correspondante
      $regionLibelle = $villeRegionMap[$centreData['ville']] ?? null;
      $region = $regions->firstWhere('libelle', $regionLibelle);

      if (!$region) {
        $this->command->warn("⚠️  Région non trouvée pour {$centreData['ville']}, centre ignoré.");
        continue;
      }

      Centre::updateOrCreate(
        ['libelle_centre' => $centreData['nom']],
        [
          'type_centre' => $centreData['type'],
          'ville_centre' => $centreData['ville'],
          'region_id' => $region->id,
          'capacite' => $faker->numberBetween(200, 800),
          'est_actif' => true,
          'responsable_id' => null,
        ]
      );
    }

    $this->command->info("✅ " . count($centres) . " centres créés avec succès!");
  }
}
