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
        Schema::create('admission_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('concours_id');
            $table->uuid('session_id')->nullable();
            
            // Seuils d'admission
            $table->decimal('seuil_admission_standard', 4, 2)->default(10.00)
                ->comment('Seuil pour admission standard (ex: 10/20)');
            $table->decimal('seuil_admission_minimum', 4, 2)->default(8.00)
                ->comment('Seuil minimum pour admission conditionnelle (ex: 8/20)');
            
            // Configuration admission conditionnelle
            $table->boolean('permet_admission_conditionnelle')->default(false)
                ->comment('Autoriser admissions conditionnelles (moyenne < standard)');
            $table->integer('pourcentage_places_conditionnelles')->default(10)
                ->comment('% max de places pour admissions conditionnelles (10-15%)');
            
            // Critères de départage
            $table->json('criteres_prioritaires')->nullable()
                ->comment('Ordre des critères: age, region, matieres_principales');
            
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('concours_id')->references('id')->on('concours')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
            
            // Indexes
            $table->index(['concours_id', 'session_id']);
            $table->index('est_actif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_rules');
    }
};
