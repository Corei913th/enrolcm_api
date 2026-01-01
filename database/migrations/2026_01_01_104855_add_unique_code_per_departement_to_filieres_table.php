<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('filieres', function (Blueprint $table) {
            $table->unique(['departement_id', 'code_filiere'], 'filieres_departement_code_unique');
        });
    }

    public function down(): void
    {
        Schema::table('filieres', function (Blueprint $table) {
            $table->dropUnique('filieres_departement_code_unique');
        });
    }
};
