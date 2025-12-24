<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('niveau_matiere', function (Blueprint $table) {
            $table->uuid('niveau_id');
            $table->uuid('matiere_id');
            $table->timestamp('created_at')->useCurrent();
            
            $table->primary(['niveau_id', 'matiere_id']);
            $table->foreign('niveau_id')->references('id')->on('niveaux')->onDelete('cascade');
            $table->foreign('matiere_id')->references('id')->on('matieres')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('niveau_matiere');
    }
};
