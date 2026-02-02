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
        Schema::create('resultat_publications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('concours_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('session_id')->constrained()->onDelete('cascade');
            $table->datetime('date_publication_prevue')->nullable()->comment('Date prévue pour la publication (pour le timer)');
            $table->datetime('date_publication_effective')->nullable()->comment('Date réelle de publication');
            $table->boolean('est_publie')->default(false)->comment('Indique si les résultats sont actuellement publiés');
            $table->text('message_candidat')->nullable()->comment('Message personnalisé affiché aux candidats');
            $table->boolean('timer_actif')->default(false)->comment('Active le compte à rebours avant publication');
            $table->timestamps();
            
            // Index et contraintes
            $table->unique(['concours_id', 'session_id'], 'unique_concours_session');
            $table->index('est_publie');
            $table->index('timer_actif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultat_publications');
    }
};
