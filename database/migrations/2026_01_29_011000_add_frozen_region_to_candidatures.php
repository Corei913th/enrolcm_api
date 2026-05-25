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
        Schema::table('candidatures', function (Blueprint $table) {
            // Add frozen region column
            $table->string('region_figee')->nullable()->after('statut_candidature');
            $table->timestamp('date_figement_region')->nullable()->after('region_figee');

            // Add index for performance
            $table->index('region_figee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidatures', function (Blueprint $table) {
            $table->dropIndex(['region_figee']);
            $table->dropColumn(['region_figee', 'date_figement_region']);
        });
    }
};
