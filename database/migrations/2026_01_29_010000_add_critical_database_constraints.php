<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ✅ FIX FAILLE #10: Add critical database constraints
     */
    public function up(): void
    {
        // 1. CHECK constraint for note values (0-20)
        DB::statement('ALTER TABLE notes ADD CONSTRAINT check_note_value CHECK (valeur >= 0 AND valeur <= 20)');
        
        // 2. UNIQUE constraint for candidature (prevent double registration)
        DB::statement('
            CREATE UNIQUE INDEX idx_candidature_unique 
            ON candidatures(concours_id, session_id, candidat_id) 
            WHERE deleted_at IS NULL
        ');
        
        // 3. UNIQUE constraint for notes (prevent duplicate notes for same epreuve)
        DB::statement('
            CREATE UNIQUE INDEX idx_note_unique
            ON notes(candidature_id, epreuve_id)
            WHERE deleted_at IS NULL
        ');
        
        // 4. Foreign key constraints with CASCADE
        Schema::table('notes', function (Blueprint $table) {
            // Drop existing foreign key if exists
            try {
                $table->dropForeign(['candidature_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
            
            // Add foreign key with CASCADE
            $table->foreign('candidature_id')
                ->references('id')
                ->on('candidatures')
                ->onDelete('cascade');
        });
        
        Schema::table('resultats_finaux', function (Blueprint $table) {
            // Drop existing foreign key if exists
            try {
                $table->dropForeign(['candidature_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
            
            // Add foreign key with CASCADE
            $table->foreign('candidature_id')
                ->references('id')
                ->on('candidatures')
                ->onDelete('cascade');
        });
        
        // 5. CHECK constraint for candidature status (valid enum values)
        $validStatuses = ['BROUILLON', 'SOUMISE', 'DOCUMENTS_VERIFIES', 'PAIEMENT_VERIFIE', 'VALIDE', 'REJETEE', 'ANNULEE'];
        $statusList = "'" . implode("','", $validStatuses) . "'";
        DB::statement("ALTER TABLE candidatures ADD CONSTRAINT check_statut_candidature CHECK (statut_candidature IN ($statusList))");
        
        // 6. CHECK constraint for payment status (using actual enum values)
        $validPaymentStatuses = ['PENDING', 'VERIFIED', 'REJECTED', 'OCR_VERIFIE', 'PENDING_MANUAL_REVIEW'];
        $paymentStatusList = "'" . implode("','", $validPaymentStatuses) . "'";
        DB::statement("ALTER TABLE paiements ADD CONSTRAINT check_statut_paiement CHECK (statut IN ($paymentStatusList))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop CHECK constraints
        DB::statement('ALTER TABLE notes DROP CONSTRAINT IF EXISTS check_note_value');
        DB::statement('ALTER TABLE candidatures DROP CONSTRAINT IF EXISTS check_statut_candidature');
        DB::statement('ALTER TABLE paiements DROP CONSTRAINT IF EXISTS check_statut_paiement');
        
        // Drop UNIQUE indexes
        DB::statement('DROP INDEX IF EXISTS idx_candidature_unique');
        DB::statement('DROP INDEX IF EXISTS idx_note_unique');
        
        // Drop foreign keys
        Schema::table('notes', function (Blueprint $table) {
            try {
                $table->dropForeign(['candidature_id']);
            } catch (\Exception $e) {
                // Ignore if doesn't exist
            }
        });
        
        Schema::table('resultats_finaux', function (Blueprint $table) {
            try {
                $table->dropForeign(['candidature_id']);
            } catch (\Exception $e) {
                // Ignore if doesn't exist
            }
        });
    }
};
