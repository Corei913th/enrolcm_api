<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('convocations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('candidature_id');
            $table->string('numero_convocation', 50)->unique();
            $table->text('qr_code')->nullable();
            $table->string('fichier_pdf_url', 500)->nullable();
            $table->timestamp('date_generation')->useCurrent();
            $table->boolean('est_telechargee')->default(false);
            $table->timestamp('date_telechargement')->nullable();
            $table->boolean('est_envoyee')->default(false);
            $table->timestamp('date_envoi')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            
            $table->foreign('candidature_id')->references('id')->on('candidatures')->onDelete('cascade');
            
            $table->index('candidature_id');
            $table->index('numero_convocation');
            $table->index('est_telechargee');
            $table->index('est_envoyee');
        });
    }

    public function down()
    {
        Schema::dropIfExists('convocations');
    }
};
