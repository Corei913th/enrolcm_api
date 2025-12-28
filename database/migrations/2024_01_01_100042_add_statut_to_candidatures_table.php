<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('candidatures', function (Blueprint $table) {
            $table->enum('statut_inscription', ['BROUILLON', 'SUSPENDUE', 'CONFIRMEE', 'INVALIDEE'])
                ->default('BROUILLON')
                ->after('code_cand_def');
        });
    }

    public function down()
    {
        Schema::table('candidatures', function (Blueprint $table) {
            $table->dropColumn('statut_inscription');
        });
    }
};
