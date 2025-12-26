<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('candidat_id')->nullable(); // Nullable car rempli après inscription
            $table->string('numero_recu')->unique();
            $table->string('banque')->nullable();
            $table->decimal('montant', 10, 2)->default(0);
            $table->date('date_paiement')->nullable();
            $table->string('image_path');
            $table->json('ocr_data')->nullable();
            $table->enum('statut_verification', ['en_attente', 'verifie', 'rejete'])->default('en_attente');
            $table->text('motif_rejet')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->uuid('verified_by')->nullable();
            $table->timestamps();
            
            $table->foreign('candidat_id')->references('utilisateur_id')->on('candidats')->onDelete('restrict');
            $table->foreign('verified_by')->references('id')->on('utilisateurs')->onDelete('set null');
            
            $table->index('candidat_id');
            $table->index('statut_verification');
            $table->index('numero_recu');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_receipts');
    }
};
