<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // CONTRAINTE MÉTIER : Unicité PRU dans utilisateurs
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->unique('user_name', 'unique_pru'); // PRU doit être unique
        });

        // CONTRAINTE MÉTIER : Unicité numéro CNI dans candidats
        Schema::table('candidats', function (Blueprint $table) {
            $table->unique('numero_cni', 'unique_cni'); // CNI doit être unique
        });

        // CONTRAINTE MÉTIER : Un paiement par PRU et concours
        Schema::table('paiements', function (Blueprint $table) {
            $table->unique(['reference', 'concours_id'], 'unique_pru_concours');
        });

        // CONTRAINTE MÉTIER : Un numéro de convocation unique
        Schema::table('convocations', function (Blueprint $table) {
            $table->unique('numero_convocation', 'unique_convocation');
        });

        // INDEX MÉTIER : Optimiser les recherches fréquentes
        Schema::table('candidatures', function (Blueprint $table) {
            $table->index(['concours_id', 'session_id', 'statut_candidature'], 'idx_candidatures_workflow');
            $table->index(['centre_id', 'statut_candidature'], 'idx_candidatures_affectation');
        });

        Schema::table('paiements', function (Blueprint $table) {
            $table->index(['statut', 'created_at'], 'idx_paiements_validation');
            $table->index(['concours_id', 'statut'], 'idx_paiements_concours');
        });
    }

    public function down()
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->dropUnique('unique_pru');
        });

        Schema::table('candidats', function (Blueprint $table) {
            $table->dropUnique('unique_cni');
        });

        Schema::table('paiements', function (Blueprint $table) {
            $table->dropUnique('unique_pru_concours');
        });

        Schema::table('convocations', function (Blueprint $table) {
            $table->dropUnique('unique_convocation');
        });

        Schema::table('candidatures', function (Blueprint $table) {
            $table->dropIndex('idx_candidatures_workflow');
            $table->dropIndex('idx_candidatures_affectation');
        });

        Schema::table('paiements', function (Blueprint $table) {
            $table->dropIndex('idx_paiements_validation');
            $table->dropIndex('idx_paiements_concours');
        });
    }
};
