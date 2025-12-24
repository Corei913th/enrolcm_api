<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('etat_concours_session', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('concours_session_concours_id')->nullable();
            $table->uuid('concours_session_session_id')->nullable();
            $table->uuid('etat_session_id')->nullable();
            $table->timestamp('date_etat')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('etat_session_id')->references('id')->on('etat_session');
        });
    }

    public function down()
    {
        Schema::dropIfExists('etat_concours_session');
    }
};
