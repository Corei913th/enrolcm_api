<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('filieres', function (Blueprint $table) {
            $table->uuid('id')->primary();
             $table->string('code_filiere', 10);
            $table->string('libelle_filiere', 200);
            $table->uuid('departement_id')->nullable();
            $table->text('desc_filiere')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            // Contraintes
            $table->foreign('departement_id')->references('id')->on('departements')->onDelete('restrict');
            $table->index('departement_id');
            $table->index('est_actif');

                // Unicité par département
                $table->unique(['departement_id', 'code_filiere'], 'filieres_departement_code_unique');
            });
    }

    public function down()
    {
        Schema::dropIfExists('filieres');
    }
};
