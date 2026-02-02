<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('resultats_finaux', function (Blueprint $table) {
            // Catégorie d'admission pour traçabilité
            $table->enum('categorie_admission', ['STANDARD', 'CONDITIONNEL', 'ELIMINATOIRE'])
                ->nullable()
                ->after('decision')
                ->comment('STANDARD: >= 10, CONDITIONNEL: 8-10, ELIMINATOIRE: note éliminatoire');
            
            // Score de départage (pour critères prioritaires)
            $table->decimal('score_departage', 10, 4)
                ->nullable()
                ->after('rang')
                ->comment('Score calculé pour départage (age + region + matieres)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resultats_finaux', function (Blueprint $table) {
            $table->dropColumn(['categorie_admission', 'score_departage']);
        });
    }
};
