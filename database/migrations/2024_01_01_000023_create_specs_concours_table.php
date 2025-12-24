<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('specs_concours', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('desc_infos_concours')->nullable();
            $table->boolean('carte_nationale_identite')->default(true);
            $table->boolean('diplomes')->default(true);
            $table->boolean('certificat_nationalite')->default(true);
            $table->boolean('releve_notes')->default(true);
            $table->boolean('acte_naissance')->default(true);
            $table->boolean('photo')->default(true);
            $table->decimal('montant_frais_depot', 10, 2)->default(0);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('specs_concours');
    }
};
