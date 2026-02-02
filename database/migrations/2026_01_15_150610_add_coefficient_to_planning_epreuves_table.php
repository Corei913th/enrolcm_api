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
        Schema::table('planning_epreuves', function (Blueprint $table) {
            // Coefficient spécifique pour ce concours (override le coefficient_defaut de l'épreuve)
            $table->integer('coefficient')->nullable()->after('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planning_epreuves', function (Blueprint $table) {
            $table->dropColumn('coefficient');
        });
    }
};
