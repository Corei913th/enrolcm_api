<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('candidature_salle', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('candidature_id');
            $table->uuid('salle_id')->nullable();
            $table->uuid('planning_epreuve_id')->nullable();
            $table->string('numero_place', 10)->nullable();
            $table->boolean('est_present')->default(false);
            $table->timestamp('heure_arrivee')->nullable();
            $table->text('observations')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            
            $table->foreign('candidature_id')->references('id')->on('candidatures')->onDelete('cascade');
            $table->foreign('salle_id')->references('id')->on('salles_examen')->onDelete('set null');
            $table->foreign('planning_epreuve_id')->references('id')->on('planning_epreuves')->onDelete('set null');
            
            // Pas de contrainte unique - l'admin peut réaffecter manuellement
            $table->index('candidature_id');
            $table->index('salle_id');
            $table->index('planning_epreuve_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('candidature_salle');
    }
};
