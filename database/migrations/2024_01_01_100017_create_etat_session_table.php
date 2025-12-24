<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\EtatSession;

return new class extends Migration
{
    public function up()
    {
        Schema::create('etat_session', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('libelle_etat', EtatSession::values())->default(EtatSession::OUVERTE);
            $table->text('desc_etat')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('etat_session');
    }
};
