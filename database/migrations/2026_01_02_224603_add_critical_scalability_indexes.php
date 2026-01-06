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
        // INDEX CRITIQUES POUR SCALABILITÉ AVEC GROS VOLUMES

        // Index composite pour recherche rapide des paiements disponibles (inscription)
        Schema::table('paiements', function (Blueprint $table) {
            $table->index(['statut', 'reference'], 'idx_paiements_available_pru')
                ->whereNull('candidat_id');
        });

        // Index pour les statistiques et rapports (gros volumes)
        Schema::table('paiements', function (Blueprint $table) {
            $table->index(['concours_id', 'statut', 'created_at'], 'idx_paiements_stats_reporting');
        });


        Schema::table('paiements', function (Blueprint $table) {
            $table->index(['created_at', 'statut'], 'idx_paiements_archiving')
                ->where('created_at', '<', now()->subMonths(12));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropIndex('idx_paiements_available_pru');
            $table->dropIndex('idx_paiements_stats_reporting');
            $table->dropIndex('idx_paiements_archiving');
        });
    }
};
