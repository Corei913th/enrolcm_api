<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('matieres', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_matiere', 10)->unique();
            $table->string('libelle_matiere', 200);
            $table->integer('coefficient')->nullable()->default(2);
            $table->boolean('est_actif')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('matieres');
    }
};
