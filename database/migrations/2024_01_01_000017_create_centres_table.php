<?php

use App\Enums\TypeCentre;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('centres', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('libelle_centre', 200);
            $table->enum('type_centre', TypeCentre::values())->nullable();
            $table->string('ville_centre', 100)->nullable();
            $table->integer('capacite')->default(0);
            $table->boolean('est_actif')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->uuid('responsable_id')->nullable();

            $table->foreign('responsable_id')->references('utilisateur_id')->on('responsables_centre')->onDelete('set null');

            $table->index('ville_centre');
            $table->index('est_actif');
            $table->index(['ville_centre', 'est_actif']);
            $table->index('responsable_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('centres');
    }
};
