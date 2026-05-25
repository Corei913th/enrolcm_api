<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('candidats', function (Blueprint $table) {

            $table->string('lieu_naissance_cand', 100)->nullable()->after('date_naissance_cand');
            $table->string('departement', 100)->nullable()->after('region');
            $table->string('arrondissement', 100)->nullable()->after('departement');

            $table->string('etablissement_origine', 200)->nullable()->after('filiere_origine');
            $table->string('ville_etablissement', 100)->nullable()->after('etablissement_origine');
            $table->string('serie_bac', 10)->nullable()->after('diplome_admission');
            $table->integer('annee_obtention_bac')->nullable()->after('serie_bac');

            $table->uuid('filiere_id')->nullable()->after('code_cand');

            $table->boolean('a_handicap')->default(false)->after('sexe_cand');
            $table->string('type_handicap', 255)->nullable()->after('a_handicap');

            $table->foreign('filiere_id')->references('id')->on('filieres')->onDelete('set null');

            $table->index('lieu_naissance_cand');
            $table->index('departement');
            $table->index('etablissement_origine');
            $table->index('serie_bac');
            $table->index('filiere_id');
        });

        Schema::table('candidats', function (Blueprint $table) {
            $table->dropColumn('handicap');
        });
    }

    public function down()
    {
        Schema::table('candidats', function (Blueprint $table) {
            $table->dropForeign(['filiere_id']);

            $table->dropColumn([
                'lieu_naissance_cand',
                'departement',
                'arrondissement',
                'etablissement_origine',
                'ville_etablissement',
                'serie_bac',
                'annee_obtention_bac',
                'filiere_id',
                'a_handicap',
                'type_handicap',
            ]);

            $table->text('handicap')->nullable();
        });
    }
};
