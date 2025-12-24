<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\EtatCandidature;

return new class extends Migration
{
    public function up()
    {
        Schema::create('etats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('libelle_etat', EtatCandidature::values());
            $table->text('desc_etat')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('etats');
    }
};
