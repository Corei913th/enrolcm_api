<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('departements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_departement', 10)->unique();
            $table->string('libelle_departement', 200);
            $table->uuid('ecole_id')->nullable();
            $table->text('desc_departement')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            
            $table->foreign('ecole_id')->references('id')->on('ecoles')->onDelete('restrict');
            $table->index('ecole_id');
            $table->index('est_actif');
        });
    }

    public function down()
    {
        Schema::dropIfExists('departements');
    }
};
