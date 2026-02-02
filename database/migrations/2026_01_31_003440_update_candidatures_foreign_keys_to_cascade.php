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
            // Drop the old foreign key with restrict
            $table->dropForeign(['candidat_id']);

            // Re-create it with cascade
            $table->foreign('candidat_id')
                ->references('utilisateur_id')
                ->on('candidats')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidatures', function (Blueprint $table) {
            $table->dropForeign(['candidat_id']);

            $table->foreign('candidat_id')
                ->references('utilisateur_id')
                ->on('candidats')
                ->onDelete('restrict');
        });
    }
};
