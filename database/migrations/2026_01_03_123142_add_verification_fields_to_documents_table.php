<?php

use App\Enums\StatutVerificationDocument;
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
        Schema::table('documents', function (Blueprint $table) {
            $table->uuid('document_requis_id')->nullable()->after('candidature_id');
            $table->foreign('document_requis_id')->references('id')->on('documents_requis')->onDelete('cascade');

            $table->enum(
                'statut_verification',
                StatutVerificationDocument::values()
            )->default(StatutVerificationDocument::EN_ATTENTE->value)->after('type_document');

            $table->text('commentaire_verification')->nullable()->after('statut_verification');
            $table->uuid('valide_par')->nullable()->after('commentaire_verification');
            $table->foreign('valide_par')->references('id')->on('utilisateurs')->onDelete('set null');
            $table->timestamp('date_verification')->nullable()->after('valide_par');

            $table->unique(['candidature_id', 'document_requis_id']);
            $table->index('statut_verification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropUnique(['candidature_id', 'document_requis_id']);
            $table->dropIndex(['statut_verification']);
            $table->dropForeign(['document_requis_id']);
            $table->dropForeign(['valide_par']);
            $table->dropColumn([
                'document_requis_id',
                'statut_verification',
                'commentaire_verification',
                'valide_par',
                'date_verification'
            ]);
        });
    }
};
