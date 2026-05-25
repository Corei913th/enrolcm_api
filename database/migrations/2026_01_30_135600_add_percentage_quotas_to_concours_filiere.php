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
        Schema::table('concours_filiere', function (Blueprint $table) {
            // Type de quota : ABSOLUTE (nombre fixe) ou PERCENTAGE (pourcentage)
            $table->enum('quota_type', ['ABSOLUTE', 'PERCENTAGE'])
                ->default('PERCENTAGE')
                ->after('nombre_places')
                ->comment('Type de quota régional');

            // Pourcentage pour quotas régionaux (ex: 30.5 pour 30.5%)
            $table->decimal('quota_percentage', 5, 2)
                ->nullable()
                ->after('quota_type')
                ->comment('Pourcentage pour quota régional (si quota_type=PERCENTAGE)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('concours_filiere', function (Blueprint $table) {
            $table->dropColumn(['quota_type', 'quota_percentage']);
        });
    }
};
