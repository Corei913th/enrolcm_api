<?php

namespace Database\Seeders;

use App\Enums\TypeDocument;
use App\Models\Concours;
use App\Models\DocumentRequis;
use Illuminate\Database\Seeder;

class DocumentRequisSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📄 Génération des documents requis...');

        $concours = Concours::all();

        if ($concours->isEmpty()) {
            $this->command->warn('⚠️  Aucun concours trouvé. Les documents requis seront créés plus tard.');

            return;
        }

        // Documents requis standards pour tous les concours
        $documentsStandards = [
            [
                'type_document' => TypeDocument::CNI,
                'nom_document' => 'Carte Nationale d\'Identité',
                'description' => 'Copie certifiée de la CNI en cours de validité',
                'est_obligatoire' => true,
                'ordre_affichage' => 1,
            ],
            [
                'type_document' => TypeDocument::ACTE_NAISSANCE,
                'nom_document' => 'Acte de Naissance',
                'description' => 'Acte de naissance original de moins de 3 mois',
                'est_obligatoire' => true,
                'ordre_affichage' => 2,
            ],
            [
                'type_document' => TypeDocument::DIPLOME,
                'nom_document' => 'Diplôme du Baccalauréat',
                'description' => 'Copie certifiée du diplôme du baccalauréat',
                'est_obligatoire' => true,
                'ordre_affichage' => 3,
            ],
            [
                'type_document' => TypeDocument::RELEVE_NOTE,
                'nom_document' => 'Relevé de Notes du Baccalauréat',
                'description' => 'Relevé de notes officiel du baccalauréat',
                'est_obligatoire' => true,
                'ordre_affichage' => 4,
            ],
            [
                'type_document' => TypeDocument::PHOTO_IDENTITE,
                'nom_document' => 'Photo d\'Identité',
                'description' => '4 photos d\'identité récentes (4x4 cm)',
                'est_obligatoire' => true,
                'ordre_affichage' => 5,
            ],
            [
                'type_document' => TypeDocument::CERTIFICAT_MEDICAL,
                'nom_document' => 'Certificat Médical',
                'description' => 'Certificat médical d\'aptitude physique de moins de 3 mois',
                'est_obligatoire' => true,
                'ordre_affichage' => 6,
            ],
            [
                'type_document' => TypeDocument::ATTESTATION_REUSSITE,
                'nom_document' => 'Attestation de Réussite',
                'description' => 'Attestation de réussite au baccalauréat (si diplôme non encore disponible)',
                'est_obligatoire' => false,
                'ordre_affichage' => 7,
            ],
            [
                'type_document' => TypeDocument::AUTRE,
                'nom_document' => 'Certificat de Nationalité',
                'description' => 'Certificat de nationalité camerounaise (pour les candidats nés hors du Cameroun)',
                'est_obligatoire' => false,
                'ordre_affichage' => 8,
            ],
        ];

        $count = 0;

        // Créer les documents requis pour chaque concours
        foreach ($concours as $concoursItem) {
            foreach ($documentsStandards as $doc) {
                DocumentRequis::updateOrCreate(
                    [
                        'concours_id' => $concoursItem->id,
                        'type_document' => $doc['type_document'],
                    ],
                    [
                        'nom_document' => $doc['nom_document'],
                        'description' => $doc['description'],
                        'est_obligatoire' => $doc['est_obligatoire'],
                        'ordre_affichage' => $doc['ordre_affichage'],
                    ]
                );
                $count++;
            }
        }

        $this->command->info("✅ {$count} documents requis créés avec succès!");
    }
}
