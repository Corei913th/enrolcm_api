<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('responsables_centre', function (Blueprint $table) {
            $table->uuid('utilisateur_id')->primary();
            $table->string('code_agent', 50)->unique();
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('utilisateur_id')->references('id')->on('utilisateurs')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('responsables_centre');
    }
};
