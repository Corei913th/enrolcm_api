<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('concours', function (Blueprint $table) {
            $table->text('description')->nullable()->after('libelle_concours');
        });
    }

    public function down()
    {
        Schema::table('concours', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
