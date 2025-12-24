<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('filiere_niveau', function (Blueprint $table) {
            $table->uuid('filiere_id');
            $table->uuid('niveau_id');
            $table->timestamp('created_at')->useCurrent();
            
            $table->primary(['filiere_id', 'niveau_id']);
            $table->foreign('filiere_id')->references('id')->on('filieres')->onDelete('cascade');
            $table->foreign('niveau_id')->references('id')->on('niveaux')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('filiere_niveau');
    }
};
