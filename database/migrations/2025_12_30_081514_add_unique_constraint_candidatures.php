<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('candidatures', function (Blueprint $table) {
            // MVP CRITIQUE : Un candidat ne peut s'inscrire qu'une fois par concours+session
            $table->unique(['candidat_id', 'concours_id', 'session_id'], 'unique_candidature_mvp');
        });
    }

    public function down()
    {
        Schema::table('candidatures', function (Blueprint $table) {
            $table->dropUnique('unique_candidature_mvp');
        });
    }
};
