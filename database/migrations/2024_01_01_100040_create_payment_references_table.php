<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_references', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('concours_id');
            $table->uuid('candidat_id');
            $table->string('reference', 50)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
            
            $table->foreign('concours_id')->references('id')->on('concours')->onDelete('cascade');
            $table->foreign('candidat_id')->references('utilisateur_id')->on('candidats')->onDelete('cascade');
            
            $table->unique(['concours_id', 'candidat_id']);
            $table->index('reference');
            $table->index('expires_at');
            $table->index(['concours_id', 'candidat_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_references');
    }
};
