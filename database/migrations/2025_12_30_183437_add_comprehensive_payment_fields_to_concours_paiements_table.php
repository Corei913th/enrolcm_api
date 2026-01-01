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
        Schema::table('concours_paiements', function (Blueprint $table) {
            // Informations bancaires complètes
            $table->string('devise', 3)->default('XAF')->after('nom_beneficiaire');
            $table->string('code_banque', 11)->nullable()->after('devise'); // BIC/SWIFT
            $table->string('agence_banque', 100)->nullable()->after('code_banque');
            $table->string('iban', 34)->nullable()->after('agence_banque');

            // Configuration paiement
            $table->enum('type_paiement', ['virement', 'cheque', 'mobile_money', 'especes', 'carte_bancaire'])
                ->default('virement')->after('iban');
            $table->json('banques_acceptees')->nullable()->after('type_paiement');
            $table->decimal('frais_paiement', 10, 2)->default(0)->after('banques_acceptees');

            // Validation et sécurité
            $table->string('reference_format', 255)->nullable()->after('frais_paiement');
            $table->decimal('minimum_confiance_ocr', 5, 2)->default(85.00)->after('reference_format');
            $table->boolean('validation_auto')->default(true)->after('minimum_confiance_ocr');

            // Métadonnées
            $table->text('commentaires')->nullable()->after('validation_auto');
            $table->timestamp('date_derniere_modification')->nullable()->after('commentaires');

            // Index pour optimisation
            $table->index(['type_paiement', 'est_actif']);
            $table->index('devise');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('concours_paiements', function (Blueprint $table) {
            // Supprimer les index
            $table->dropIndex(['type_paiement', 'est_actif']);
            $table->dropIndex(['devise']);

            // Supprimer les colonnes ajoutées
            $table->dropColumn([
                'devise',
                'code_banque',
                'agence_banque',
                'iban',
                'type_paiement',
                'banques_acceptees',
                'frais_paiement',
                'reference_format',
                'minimum_confiance_ocr',
                'validation_auto',
                'commentaires',
                'date_derniere_modification'
            ]);
        });
    }
};
