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
        Schema::table('epreuves', function (Blueprint $table) {
            $table->integer('coefficient_defaut')->default(1)->after('duree_en_minute');
            $table->decimal('note_eliminatoire', 4, 2)->nullable()->after('coefficient_defaut');
            $table->boolean('est_eliminatoire')->default(false)->after('note_eliminatoire');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('epreuves', function (Blueprint $table) {
            $table->dropColumn(['coefficient_defaut', 'note_eliminatoire', 'est_eliminatoire']);
        });
    }
};
