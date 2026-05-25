<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('specs_concours', function (Blueprint $table) {
            // Ajouter nom pour identifier la spec
            $table->string('nom_spec', 100)->after('id');

            // Remplacer les booléens par un JSON flexible
            $table->json('documents_requis')->nullable()->after('photo');

            // Ajouter critères d'éligibilité
            $table->integer('age_minimum')->nullable()->after('montant_frais_depot');
            $table->integer('age_maximum')->nullable()->after('age_minimum');
            $table->json('series_bac_acceptees')->nullable()->after('age_maximum');
            $table->json('nationalites_acceptees')->nullable()->after('series_bac_acceptees');

            // Statut actif
            $table->boolean('est_actif')->default(true)->after('updated_at');
        });

        // Supprimer les anciens champs booléens (remplacés par documents_requis JSON)
        Schema::table('specs_concours', function (Blueprint $table) {
            $table->dropColumn([
                'carte_nationale_identite',
                'diplomes',
                'certificat_nationalite',
                'releve_notes',
                'acte_naissance',
                'photo',
            ]);
        });
    }

    public function down()
    {
        Schema::table('specs_concours', function (Blueprint $table) {
            // Restaurer les anciens champs
            $table->boolean('carte_nationale_identite')->default(true);
            $table->boolean('diplomes')->default(true);
            $table->boolean('certificat_nationalite')->default(true);
            $table->boolean('releve_notes')->default(true);
            $table->boolean('acte_naissance')->default(true);
            $table->boolean('photo')->default(true);

            // Supprimer les nouveaux champs
            $table->dropColumn([
                'nom_spec',
                'documents_requis',
                'age_minimum',
                'age_maximum',
                'series_bac_acceptees',
                'nationalites_acceptees',
                'est_actif',
            ]);
        });
    }
};
