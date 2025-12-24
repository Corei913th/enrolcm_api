<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('concours', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('libelle_concours', 200);
            $table->date('date_limite_depot');
            $table->date('date_examen');
            $table->integer('nbre_max_places')->default(0);
            $table->decimal('frais_inscription', 10, 2)->default(0);
            $table->boolean('est_actif')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            
            $table->index('est_actif');
            $table->index('date_limite_depot');
            $table->index('date_examen');
            $table->index(['est_actif', 'date_limite_depot']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('concours');
    }
};
