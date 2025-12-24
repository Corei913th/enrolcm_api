<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\StatutNote;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('candidature_id');
            $table->uuid('epreuve_id');
            $table->decimal('valeur', 4, 2);
            $table->timestamp('date_saisie')->useCurrent();
            $table->boolean('est_definitive')->default(false);
            $table->boolean('est_eliminatoire')->default(false);
            $table->enum('statut', StatutNote::values())->default(StatutNote::EN_ATTENTE_SAISIE);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            
            $table->foreign('candidature_id')->references('id')->on('candidatures')->onDelete('restrict');
            $table->foreign('epreuve_id')->references('id_epreuve')->on('epreuves')->onDelete('restrict');
            
            $table->index('candidature_id');
            $table->index('epreuve_id');
            $table->index('deleted_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notes');
    }
};
