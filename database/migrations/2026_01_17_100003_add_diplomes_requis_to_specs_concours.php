<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('specs_concours', function (Blueprint $table) {
            $table->json('diplomes_requis')->nullable()->after('nationalites_acceptees')
                ->comment('Liste des diplômes requis pour admission parallèle');

            $table->text('criteres_admission_supplementaires')->nullable()
                ->after('diplomes_requis')
                ->comment('Critères additionnels d\'admission');

            $table->boolean('accepte_diplomes_equivalents')->default(false)
                ->after('criteres_admission_supplementaires')
                ->comment('Accepter les diplômes équivalents reconnus');

            $table->boolean('accepte_candidats_en_cours')->default(false)
                ->after('accepte_diplomes_equivalents')
                ->comment('Accepter les candidats préparant le diplôme');
        });
    }

    public function down()
    {
        Schema::table('specs_concours', function (Blueprint $table) {
            $table->dropColumn([
                'diplomes_requis',
                'criteres_admission_supplementaires',
                'accepte_diplomes_equivalents',
                'accepte_candidats_en_cours',
            ]);
        });
    }
};
