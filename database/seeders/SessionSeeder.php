<?php

namespace Database\Seeders;

use App\Enums\EtatSession;
use Illuminate\Database\Seeder;
use App\Models\EtatSession as EtatSessionModel;
use App\Models\Session;

class SessionSeeder extends Seeder
{
    /**
     * Exécuter le seeder.
     *
     * @return void
     */
    public function run(): void
    {
        
        
        Session::create([
            'libelle_session' => '2025-2026',
            'est_actif' => true,
        ]);


        EtatSessionModel::create([
            'libelle_etat' => EtatSession::OUVERTE,
            'desc_etat' => 'La session est ouverte pour les inscriptions.',
        ]);

        EtatSessionModel::create([
            'libelle_etat' => EtatSession::FERMEE,
            'desc_etat' => 'La session est fermée pour les inscriptions.',
        ]);


        Session::create([
            'libelle_session' => '2026-2027',
            'est_actif' => true,
        ]);



        // Ajoutez d'autres sessions si nécessaire
    }
}