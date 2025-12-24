<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('utilisateur_role', function (Blueprint $table) {
            $table->uuid('utilisateur_id');
            $table->uuid('role_id');
            $table->timestamp('created_at')->useCurrent();
            
            $table->primary(['utilisateur_id', 'role_id']);
            $table->foreign('utilisateur_id')->references('id')->on('utilisateurs')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('utilisateur_role');
    }
};
