<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('niveaux', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_niveau', 10)->unique();
            $table->string('libelle_niveau', 100);
            $table->uuid('filiere_id')->nullable();
            $table->integer('ordre')->nullable();
            $table->text('desc_niveau')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            
            $table->foreign('filiere_id')->references('id')->on('filieres')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('niveaux');
    }
};
