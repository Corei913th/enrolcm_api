<?php

use App\Enums\StatutSession;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sessions', function (Blueprint $table) {
            // MVP CRITIQUE : Statuts pour contrôler les inscriptions
            $table->enum('statut_session', StatutSession::values())
                ->default(StatutSession::BROUILLON->value)
                ->after('est_actif');

            // MVP : Dates pour contrôler automatiquement les ouvertures/fermetures
            $table->date('date_ouverture_inscription')->nullable()->after('statut_session');
            $table->date('date_fermeture_inscription')->nullable()->after('date_ouverture_inscription');

            // Index pour les queries fréquentes
            $table->index('statut_session');
            $table->index(['statut_session', 'est_actif']);
        });
    }

    public function down()
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex(['statut_session', 'est_actif']);
            $table->dropIndex(['statut_session']);
            $table->dropColumn(['date_fermeture_inscription', 'date_ouverture_inscription', 'statut_session']);
        });
    }
};
