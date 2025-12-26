<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TypeUtilisateur;

return new class extends Migration
{
    public function up()
    {
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_name', 255)->unique();
            $table->string('email', 255)->unique()->nullable();
            $table->string('mot_de_passe', 255);
            $table->string('telephone', 20)->nullable();
            $table->boolean('est_actif')->default(true);
            $table->boolean('email_verifie')->default(false);
            $table->enum('type_utilisateur', TypeUtilisateur::values());
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index('email');
            $table->index('user_name');
            $table->index('type_utilisateur');
            $table->index('est_actif');
            $table->index(['type_utilisateur', 'est_actif']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('utilisateurs');
    }
};
