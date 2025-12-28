<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ecoles', function (Blueprint $table) {
            
            $table->string('libelle_ecole_en', 200)->nullable()->after('libelle_ecole');
            
            
            $table->string('nom_directeur', 100)->nullable()->after('libelle_ecole_en');
            $table->string('titre_directeur', 100)->default('Directeur Général')->after('nom_directeur');
            $table->string('nom_institution_tutelle', 200)->nullable()->after('titre_directeur');
            $table->string('nom_institution_tutelle_en', 200)->nullable()->after('nom_institution_tutelle');
            $table->string('numero_agrement', 100)->nullable()->after('nom_institution_tutelle_en');
            $table->date('date_creation')->nullable()->after('numero_agrement');
            
            
            $table->string('logo_institution_tutelle_url', 500)->nullable()->after('logo_url');
           
            
            
            $table->text('adresse_complete')->nullable()->after('localisation');
            $table->string('ville', 100)->nullable()->after('adresse_complete');
            $table->string('fax', 20)->nullable()->after('telephone_ecole');
            $table->string('telephone_2', 20)->nullable()->after('fax');
            
            
            $table->string('slogan', 200)->nullable()->after('devise');
            
            
            $table->text('mentions_legales')->nullable()->after('embleme_ecole');
            
            // Index
            $table->index('ville');
        });
    }

    public function down()
    {
        Schema::table('ecoles', function (Blueprint $table) {
            $table->dropColumn([
                'libelle_ecole_en',
                'nom_directeur',
                'titre_directeur',
                'nom_institution_tutelle',
                'nom_institution_tutelle_en',
                'numero_agrement',
                'date_creation',
                'logo_institution_tutelle_url',
                'adresse_complete',
                'ville',
                'fax',
                'telephone_2',
                'slogan',
                'mentions_legales',
            ]);
        });
    }
};
