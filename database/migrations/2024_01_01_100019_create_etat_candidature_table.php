<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('etat_candidature', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('candidature_id');
            $table->uuid('etat_id')->nullable();
            $table->timestamp('date_etat')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('candidature_id')->references('id')->on('candidatures')->onDelete('cascade');
            $table->foreign('etat_id')->references('id')->on('etats');
        });
    }

    public function down()
    {
        Schema::dropIfExists('etat_candidature');
    }
};
