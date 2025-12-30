<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // MVP : Supprimer la duplication PRU et téléphone
        Schema::table('candidats', function (Blueprint $table) {
            // Garder seulement dans utilisateurs.user_name et utilisateurs.telephone
            $table->dropColumn('pru');
            $table->dropColumn('telephone_candidat');
        });

        // MVP : Simplifier les relations paiements
        Schema::table('paiements', function (Blueprint $table) {
            // Supprimer la contrainte nullable problématique
            $table->uuid('candidat_id')->nullable(false)->change();

            // Ajouter une vraie FK vers candidatures pour lier paiement et candidature
            $table->uuid('candidature_id')->nullable()->after('candidat_id');
            $table->foreign('candidature_id')->references('id')->on('candidatures')->onDelete('set null');
            $table->index('candidature_id');
        });
    }

    public function down()
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropForeign(['candidature_id']);
            $table->dropIndex(['candidature_id']);
            $table->dropColumn('candidature_id');
            $table->uuid('candidat_id')->nullable()->change();
        });

        Schema::table('candidats', function (Blueprint $table) {
            $table->string('pru', 50)->nullable()->after('utilisateur_id');
            $table->string('telephone_candidat', 20)->nullable()->after('pru');
        });
    }
};
