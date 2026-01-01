<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matieres', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_matiere', 10);
            $table->string('libelle_matiere', 200);
            $table->uuid('filiere_id')->nullable();
            $table->integer('coefficient')->nullable()->default(2);
            $table->boolean('est_actif')->default(true);
            $table->timestamps();

            // Foreign key
            $table->foreign('filiere_id')->references('id')->on('filieres')->onDelete('restrict');

            // Unicité par filière
            $table->unique(['filiere_id', 'code_matiere'], 'matieres_filiere_code_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matieres');
    }
};

