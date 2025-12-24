<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('candidatures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('candidat_id');
            $table->uuid('concours_id');
            $table->uuid('session_id');
            $table->timestamp('date_candidature')->useCurrent();
            $table->string('code_cand_temp', 50)->nullable();
            $table->string('code_cand_def', 50)->unique()->nullable();
            $table->text('qr_code')->nullable();
            $table->date('date_inscription')->nullable();
            $table->date('date_depot_physique')->nullable();
            $table->timestamp('date_validation')->nullable();
            $table->text('motif_rejet')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            
            $table->foreign('candidat_id')->references('utilisateur_id')->on('candidats')->onDelete('restrict');
            $table->foreign(['concours_id', 'session_id'])
                ->references(['concours_id', 'session_id'])
                ->on('concours_session')
                ->onDelete('restrict');
            
            $table->index('candidat_id');
            $table->index(['concours_id', 'session_id']);
            $table->index('date_validation');
            $table->index('deleted_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('candidatures');
    }
};
