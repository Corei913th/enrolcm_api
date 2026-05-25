<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('centres', function (Blueprint $table) {

            $table->uuid('region_id')
                ->after('id');

            $table->unsignedInteger('capacite')
                ->default(0)
                ->change();

            $table->foreign('region_id')
                ->references('id')
                ->on('regions')
                ->cascadeOnDelete();

            $table->index(['region_id', 'est_actif']);
        });
    }

    public function down(): void
    {
        Schema::table('centres', function (Blueprint $table) {

            // Suppression FK + index
            $table->dropForeign(['region_id']);
            $table->dropIndex(['region_id', 'est_actif']);

            // Suppression champ
            $table->dropColumn('region_id');

            $table->integer('capacite')
                ->default(0)
                ->change();
        });
    }
};
