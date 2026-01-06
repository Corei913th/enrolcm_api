<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('concours', function (Blueprint $table) {

            $table->date('date_limite_depot')->nullable()->change();
            $table->date('date_examen')->nullable()->change();
            $table->integer('nbre_max_places')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('concours', function (Blueprint $table) {
            // REMETTRE LES CONTRAINTES NOT NULL
            $table->date('date_limite_depot')->nullable(false)->change();
            $table->date('date_examen')->nullable(false)->change();
            $table->integer('nbre_max_places')->nullable(false)->change();
        });
    }
};
