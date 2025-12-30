<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('concours_filiere', function (Blueprint $table) {
            // AJOUT CRITIQUE : Places définies PAR SESSION, pas par concours
            $table->uuid('session_id')->nullable()->after('concours_id');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');

            // Changer la clé primaire pour inclure session_id
            $table->dropPrimary();
            $table->primary(['concours_id', 'session_id', 'filiere_id']);

            // Index pour les queries fréquentes
            $table->index(['concours_id', 'session_id']);
            $table->index(['session_id', 'filiere_id']);
        });

        // NOTE: Les données existantes devront être migrées manuellement
        // ou via un seeder/artisan command après cette migration
        // Cette migration ajoute seulement la structure nécessaire
    }

    public function down()
    {
        Schema::table('concours_filiere', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
            $table->dropPrimary();
            $table->dropColumn('session_id');
            $table->primary(['concours_id', 'filiere_id']);
        });
    }
};
