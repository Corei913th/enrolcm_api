<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('planning_epreuves', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('epreuve_id');
            $table->uuid('concours_id');
            $table->uuid('session_id');
            
            $table->date('date_epreuve');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->text('instructions')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
            
            $table->foreign('epreuve_id')->references('id_epreuve')->on('epreuves')->onDelete('restrict');
            $table->foreign(['concours_id', 'session_id'])
                ->references(['concours_id', 'session_id'])
                ->on('concours_session')
                ->onDelete('restrict');
            
            $table->index('epreuve_id');
            $table->index(['concours_id', 'session_id']);
            $table->index('date_epreuve');
            $table->index(['date_epreuve', 'heure_debut']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('planning_epreuves');
    }
};
