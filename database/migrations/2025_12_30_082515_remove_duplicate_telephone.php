<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('candidats', function (Blueprint $table) {
            // SUPPRESSION DÉFINITIVE de la duplication téléphone
            // Le téléphone doit être UNIQUEMENT dans utilisateurs.telephone
            // Vérifier si la colonne existe avant de la supprimer
            if (Schema::hasColumn('candidats', 'telephone_candidat')) {
                $table->dropColumn('telephone_candidat');
            }
        });
    }

    public function down()
    {
        Schema::table('candidats', function (Blueprint $table) {
            // Remettre la colonne en cas de rollback
            $table->string('telephone_candidat', 20)->nullable()->after('pru');
        });
    }
};
