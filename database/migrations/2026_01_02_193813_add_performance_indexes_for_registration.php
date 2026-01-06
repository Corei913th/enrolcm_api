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
        Schema::table('paiements', function (Blueprint $table) {
            // Index composite pour la recherche de paiement lors de l'inscription
            // Optimise: reference + statut + candidat_id (null check)
            $table->index(['reference', 'statut', 'candidat_id'], 'idx_paiements_registration_lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropIndex('idx_paiements_registration_lookup');
        });
    }
};
