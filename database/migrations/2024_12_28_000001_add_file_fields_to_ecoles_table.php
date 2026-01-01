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
        Schema::table('ecoles', function (Blueprint $table) {
            // Chemins des fichiers d'identité visuelle
            $table->string('logo_path', 500)->nullable()->after('logo_url');
            $table->string('embleme_path', 500)->nullable()->after('embleme_ecole');
            $table->string('header_frame_path', 500)->nullable()->after('embleme_path');
            
            // Métadonnées des fichiers
            $table->string('logo_original_name', 255)->nullable()->after('logo_path');
            $table->string('embleme_original_name', 255)->nullable()->after('embleme_path');
            $table->string('header_frame_original_name', 255)->nullable()->after('header_frame_path');
            
            // Nom de l'école en anglais (pour documents bilingues)
            $table->string('libelle_ecole_en', 200)->nullable()->after('libelle_ecole');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ecoles', function (Blueprint $table) {
            $table->dropColumn([
                'logo_path',
                'embleme_path',
                'header_frame_path',
                'logo_original_name',
                'embleme_original_name',
                'header_frame_original_name',
                'libelle_ecole_en',
            ]);
        });
    }
};
