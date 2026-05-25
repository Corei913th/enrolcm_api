<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('resultats_finaux', function (Blueprint $table) {
            $table->uuid('session_id')->nullable()->after('candidature_id');
            $table->index('session_id');
        });

        // Migrate existing data: set session_id from candidature.session_id
        // PostgreSQL syntax
        DB::statement('
            UPDATE resultats_finaux
            SET session_id = c.session_id
            FROM candidatures c
            WHERE resultats_finaux.candidature_id = c.id
            AND resultats_finaux.session_id IS NULL
        ');

        // Add foreign key constraint
        Schema::table('resultats_finaux', function (Blueprint $table) {
            $table->foreign('session_id')
                ->references('id')
                ->on('sessions')
                ->onDelete('cascade');
        });

        // Make session_id NOT NULL after data migration
        Schema::table('resultats_finaux', function (Blueprint $table) {
            $table->uuid('session_id')->nullable(false)->change();
        });

        // Add composite index for better query performance
        Schema::table('resultats_finaux', function (Blueprint $table) {
            $table->index(['session_id', 'moyenne_generale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resultats_finaux', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
            $table->dropIndex(['session_id', 'moyenne_generale']);
            $table->dropIndex(['session_id']);
            $table->dropColumn('session_id');
        });
    }
};
