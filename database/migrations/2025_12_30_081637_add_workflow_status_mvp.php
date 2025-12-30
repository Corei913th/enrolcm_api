<?php

use App\Enums\StatutCandidature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('candidatures', function (Blueprint $table) {
            // MVP : Workflow de validation réaliste
            $table->dropColumn('statut_inscription'); // Supprimer l'ancien

            $table->enum('statut_candidature', StatutCandidature::values())
                ->default(StatutCandidature::BROUILLON->value)
                ->after('code_cand_def');

            // MVP : Flags de validation
            $table->boolean('documents_complets')->default(false)->after('statut_candidature');
            $table->boolean('paiement_valide')->default(false)->after('documents_complets');

            // Index pour les queries fréquentes
            $table->index('statut_candidature');
            $table->index(['statut_candidature', 'documents_complets', 'paiement_valide']);
        });
    }

    public function down()
    {
        Schema::table('candidatures', function (Blueprint $table) {
            $table->dropIndex(['statut_candidature', 'documents_complets', 'paiement_valide']);
            $table->dropIndex(['statut_candidature']);
            $table->dropColumn(['paiement_valide', 'documents_complets', 'statut_candidature']);

            // Remettre l'ancien statut
            $table->enum('statut_inscription', ['ACTIF', 'INVALIDE'])->default('ACTIF');
        });
    }
};
