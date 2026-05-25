<?php

use App\Enums\TypeNotification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the old constraint
        DB::statement('ALTER TABLE notifications DROP CONSTRAINT IF EXISTS notifications_type_notification_check');

        // Get all valid enum values
        $validValues = array_map(fn ($value) => "'$value'", TypeNotification::values());
        $validValuesString = implode(', ', $validValues);

        // Add the new constraint with all current enum values
        DB::statement("ALTER TABLE notifications ADD CONSTRAINT notifications_type_notification_check CHECK (type_notification IN ($validValuesString))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the constraint
        DB::statement('ALTER TABLE notifications DROP CONSTRAINT IF EXISTS notifications_type_notification_check');

        // Recreate the old constraint (without DOCUMENT_VALIDE and DOCUMENT_REJETE)
        $oldValues = [
            'CANDIDATURE_SOUMISE',
            'CANDIDATURE_VALIDEE',
            'CANDIDATURE_REJETEE',
            'DOSSIER_INCOMPLET',
            'CONVOCATION_DISPONIBLE',
            'RAPPEL_EXAMEN',
            'RESULTATS_DISPONIBLES',
            'ADMISSION',
            'ECHEC',
            'LISTE_ATTENTE',
            'PAIEMENT_RECU',
            'PAIEMENT_VALIDE',
            'PAIEMENT_REJETE',
            'INFORMATION_GENERALE',
            'ALERTE',
            'RAPPEL',
        ];
        $oldValuesString = implode(', ', array_map(fn ($v) => "'$v'", $oldValues));

        DB::statement("ALTER TABLE notifications ADD CONSTRAINT notifications_type_notification_check CHECK (type_notification IN ($oldValuesString))");
    }
};
