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
        Schema::table('admission_rules', function (Blueprint $table) {
            if (!Schema::hasColumn('admission_rules', 'quotas_regionaux')) {
                $table->json('quotas_regionaux')->nullable()->after('criteres_prioritaires');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_rules', function (Blueprint $table) {
            //
        });
    }
};
