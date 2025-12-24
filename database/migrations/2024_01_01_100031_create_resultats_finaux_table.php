<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\DecisionAdmission;
use App\Enums\Mention;

return new class extends Migration
{
    public function up()
    {
        Schema::create('resultats_finaux', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('candidature_id');
            $table->decimal('moyenne_generale', 4, 2);
            $table->decimal('total_point', 6, 2)->default(0);
            $table->integer('rang')->nullable();
            $table->enum('decision', DecisionAdmission::values())->nullable();
            $table->enum('mention', Mention::values())->nullable();
            $table->boolean('est_admis')->default(false);
            $table->date('date_publication')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            
            $table->foreign('candidature_id')->references('id')->on('candidatures')->onDelete('restrict');
            $table->index('candidature_id');
            $table->index('deleted_at');
            $table->index('decision');
            $table->index('est_admis');
            $table->index('rang');
            $table->index(['decision', 'est_admis']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('resultats_finaux');
    }
};
