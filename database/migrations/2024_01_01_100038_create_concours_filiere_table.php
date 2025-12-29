<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('concours_filiere', function (Blueprint $table) {
            $table->uuid('concours_id');
            $table->uuid('filiere_id');
            $table->integer('nombre_places')->default(0);
            $table->timestamps();
            
            $table->primary(['concours_id', 'filiere_id']);
            $table->foreign('concours_id')->references('id')->on('concours')->onDelete('cascade');
            $table->foreign('filiere_id')->references('id')->on('filieres')->onDelete('cascade');
            
            $table->index('concours_id');
            $table->index('filiere_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('concours_filiere');
    }
};
