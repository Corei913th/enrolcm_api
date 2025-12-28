<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('candidat_id');
            $table->uuid('concours_id');
            $table->string('reference', 50);
            $table->decimal('montant', 10, 2);
            $table->string('preuve_paiement');
            
            // Données extraites par OCR
            $table->decimal('montant_ocr', 10, 2)->nullable();
            $table->date('date_ocr')->nullable();
            $table->string('banque_ocr', 100)->nullable();
            $table->string('reference_ocr', 50)->nullable();
            $table->decimal('ocr_confidence', 3, 2)->nullable();
            $table->json('ocr_raw_data')->nullable();
            
            $table->enum('statut', ['EN_ATTENTE', 'OCR_VERIFIE', 'VALIDE', 'REJETE'])->default('EN_ATTENTE');
            $table->text('motif_rejet')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->uuid('validated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('candidat_id')->references('utilisateur_id')->on('candidats')->onDelete('cascade');
            $table->foreign('concours_id')->references('id')->on('concours')->onDelete('cascade');
            $table->foreign('validated_by')->references('id')->on('utilisateurs')->onDelete('set null');
            
            $table->unique(['candidat_id', 'concours_id']);
            $table->index('reference');
            $table->index('statut');
            $table->index(['concours_id', 'statut']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('paiements');
    }
};
