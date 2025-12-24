<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('salles_examen', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_salle', 20);
            $table->integer('capacite');
            $table->uuid('centre_id')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('centre_id')->references('id')->on('centres')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('salles_examen');
    }
};
