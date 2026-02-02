<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Enums\TypeUtilisateur;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Récupérer les valeurs de l'enum
        $values = array_map(function ($value) {
            return "'$value'";
        }, TypeUtilisateur::values());

        $allowedValues = implode(', ', $values);



        // Tentative de suppression de l'ancienne contrainte et ajout de la nouvelle
        try {
            DB::statement("ALTER TABLE utilisateurs DROP CONSTRAINT IF EXISTS utilisateurs_type_utilisateur_check");
            DB::statement("ALTER TABLE utilisateurs ADD CONSTRAINT utilisateurs_type_utilisateur_check CHECK (type_utilisateur IN ($allowedValues))");
        } catch (\Exception $e) {
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // On remet les anciennes valeurs (sans SUPER_ADMIN)
        // Note: Cela échouera s'il y a déjà des SUPER_ADMIN en base

        $values = array_filter(TypeUtilisateur::values(), fn($v) => $v !== 'SUPER_ADMIN');
        $values = array_map(fn($v) => "'$v'", $values);
        $allowedValues = implode(', ', $values);

        try {
            DB::statement("ALTER TABLE utilisateurs DROP CONSTRAINT IF EXISTS utilisateurs_type_utilisateur_check");
            DB::statement("ALTER TABLE utilisateurs ADD CONSTRAINT utilisateurs_type_utilisateur_check CHECK (type_utilisateur IN ($allowedValues))");
        } catch (\Exception $e) {
        }
    }
};
