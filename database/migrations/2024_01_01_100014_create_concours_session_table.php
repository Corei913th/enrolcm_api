<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('concours_session', function (Blueprint $table) {
            $table->uuid('concours_id');
            $table->uuid('session_id');
            $table->timestamp('created_at')->useCurrent();
            
            $table->primary(['concours_id', 'session_id']);
            $table->foreign('concours_id')->references('id')->on('concours')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('concours_session');
    }
};
