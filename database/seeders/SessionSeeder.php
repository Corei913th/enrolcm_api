<?php

namespace Database\Seeders;

use App\Enums\StatutSession;
use App\Models\Session;
use Illuminate\Database\Seeder;

class SessionSeeder extends Seeder
{
    /**
     * Exécuter le seeder.
     */
    public function run(): void
    {
        // Session 2026-2027 OUVERTE
        Session::updateOrCreate(
            ['libelle_session' => '2026-2027'],
            [
                'est_actif' => true,
                'statut_session' => StatutSession::OUVERT,
            ]
        );

        $this->command->info('✅ Session 2026-2027 créée/mise à jour (OUVERTE)');
    }
}
