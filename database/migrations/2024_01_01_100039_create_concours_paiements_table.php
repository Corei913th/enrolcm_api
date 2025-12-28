<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('concours_paiements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('concours_id');
            $table->string('banque_nom', 100);
            $table->string('numero_compte', 50);
            $table->string('nom_beneficiaire', 200);
            $table->decimal('montant', 10, 2);
            $table->date('date_limite');
            $table->text('instructions')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
            
            $table->foreign('concours_id')->references('id')->on('concours')->onDelete('cascade');
            
            $table->index('concours_id');
            $table->index('est_actif');
        });
    }

    public function down()
    {
        Schema::dropIfExists('concours_paiements');
    }
};
