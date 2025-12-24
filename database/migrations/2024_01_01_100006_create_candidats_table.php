<?php

use App\Enums\Genre;
use App\Enums\Mention;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\RegionCameroun;

return new class extends Migration
{
    public function up()
    {
        Schema::create('candidats', function (Blueprint $table) {
            $table->uuid('utilisateur_id')->primary();
            $table->text('adresse_cand')->nullable();
            $table->string('nom_cand', 100);
            $table->string('prenom_cand', 100);
            $table->string('nationalite_cand', 50)->default('Camerounaise');
            $table->integer('age_cand')->nullable();
            $table->date('date_naissance_cand')->nullable();
            $table->string('nom_tuteur_cand', 100)->nullable();
            $table->string('telephone_tuteur_cand', 20)->nullable();
            $table->enum('sexe_cand', Genre::values())->nullable();
            $table->text('handicap')->nullable();
            $table->string('ethnie_cand', 50)->nullable();
            $table->string('nom_parent', 100)->nullable();
            $table->string('telephone_parent', 20)->nullable();
            $table->string('code_cand', 50)->unique()->nullable();
            $table->string('niveau_scolaire', 100)->nullable();
            $table->string('filiere_origine', 100)->nullable();
            $table->string('diplome_admission', 200)->nullable();
            $table->enum('mention', Mention::values())->nullable();
            $table->date('annee_diplome')->nullable();
            $table->string('numero_cni', 50)->unique()->nullable();
            $table->date('date_delivrance_cni')->nullable();
            $table->string('statut_matrimonial', 20)->nullable();
            $table->string('nom_pere', 100)->nullable();
            $table->string('telephone_pere', 20)->nullable();
            $table->string('numero_recu', 50)->unique(); 
            $table->string('telephone_candidat', 20)->nullable();
            $table->enum('region', RegionCameroun::values())->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->boolean('est_actif')->default(true);



            $table->foreign('utilisateur_id')->references('id')->on('utilisateurs')->onDelete('restrict');
            $table->index(['nom_cand', 'prenom_cand']);
            $table->index('code_cand');
            $table->index('numero_cni');
            $table->index('numero_recu');
            $table->index('date_naissance_cand');
            $table->index('est_actif');
        });
    }

    public function down()
    {
        Schema::dropIfExists('candidats');
    }
};
