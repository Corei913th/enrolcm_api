<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Utilisateur;
use App\Models\Admin;
use App\Models\Correcteur;
use App\Models\ResponsableCentre;
use App\Enums\TypeUtilisateur;

class UserSeeder extends Seeder
{
    /**
     * Exécuter le seeder.
     *
     * @return void
     */
    public function run(): void
    {
        // === Admins ===
        $admin1 = Utilisateur::create([
            'user_name' => 'admin_user1',
            'email' => 'admin1@example.com',
            'mot_de_passe' => Hash::make('Secret123!'),
            'telephone' => '690123456',
            'type_utilisateur' => TypeUtilisateur::ADMIN,
            'est_actif' => true,
            'email_verifie' => true,
        ]);
        Admin::create([
            'utilisateur_id' => $admin1->id,
            'matricule' => 'ADM001',
        ]);

        $admin2 = Utilisateur::create([
            'user_name' => 'admin_user2',
            'email' => 'admin2@example.com',
            'mot_de_passe' => Hash::make('Password456!'),
            'telephone' => '691987654',
            'type_utilisateur' => TypeUtilisateur::ADMIN,
            'est_actif' => true,
            'email_verifie' => true,
        ]);
        Admin::create([
            'utilisateur_id' => $admin2->id,
            'matricule' => 'ADM002',
        ]);

        // === Correcteurs ===
        $correcteur1 = Utilisateur::create([
            'user_name' => 'correcteur_user1',
            'email' => 'correcteur1@example.com',
            'mot_de_passe' => Hash::make('Correct123!'),
            'telephone' => '652123456',
            'type_utilisateur' => TypeUtilisateur::CORRECTEUR,
            'est_actif' => true,
            'email_verifie' => true,
        ]);
        Correcteur::create([
            'utilisateur_id' => $correcteur1->id,
            'matricule_enseignant' => 'ENS001',
            'specialite' => 'Mathématiques',
        ]);

        $correcteur2 = Utilisateur::create([
            'user_name' => 'correcteur_user2',
            'email' => 'correcteur2@example.com',
            'mot_de_passe' => Hash::make('Correct456!'),
            'telephone' => '653987654',
            'type_utilisateur' => TypeUtilisateur::CORRECTEUR,
            'est_actif' => true,
            'email_verifie' => true,
        ]);
        Correcteur::create([
            'utilisateur_id' => $correcteur2->id,
            'matricule_enseignant' => 'ENS002',
            'specialite' => 'Physique',
        ]);

        // === Responsables de centre ===
        $resp1 = Utilisateur::create([
            'user_name' => 'respcentre_user1',
            'email' => 'respcentre1@example.com',
            'mot_de_passe' => Hash::make('Centre123!'),
            'telephone' => '222123456',
            'type_utilisateur' => TypeUtilisateur::RESPONSABLE_CENTRE,
            'est_actif' => true,
            'email_verifie' => true,
        ]);
        ResponsableCentre::create([
            'utilisateur_id' => $resp1->id,
            'code_agent' => 'RC001',
        ]);

        $resp2 = Utilisateur::create([
            'user_name' => 'respcentre_user2',
            'email' => 'respcentre2@example.com',
            'mot_de_passe' => Hash::make('Centre456!'),
            'telephone' => '223987654',
            'type_utilisateur' => TypeUtilisateur::RESPONSABLE_CENTRE,
            'est_actif' => true,
            'email_verifie' => true,
        ]);
        ResponsableCentre::create([
            'utilisateur_id' => $resp2->id,
            'code_agent' => 'RC002',
        ]);
    }
}