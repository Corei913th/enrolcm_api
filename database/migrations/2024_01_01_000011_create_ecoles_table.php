<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\RegionCameroun;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ecoles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_ecole', 20)->unique();
            $table->string('libelle_ecole', 200);
            $table->enum('region', RegionCameroun::values())->nullable();
            $table->string('localisation', 200)->nullable();
            $table->string('logo_url', 500)->nullable();
            $table->string('bp_ecole', 50)->nullable();
            $table->string('email_ecole', 100)->nullable();
            $table->string('siteweb_ecole', 200)->nullable();
            $table->string('devise', 100)->nullable();
            $table->string('telephone_ecole', 20)->nullable();
            $table->string('embleme_ecole', 500)->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            
            $table->index('region');
            $table->index('est_actif');
            $table->index(['region', 'est_actif']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ecoles');
    }
};
