<?php

use App\Enums\RegionCameroun;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('centres', function (Blueprint $table) {
            // AJOUT GÉOGRAPHIE CAMEROUNAISE
            $table->enum('region', RegionCameroun::values())->nullable()->after('ville_centre');
            $table->string('departement', 100)->nullable()->after('region');
            $table->string('arrondissement', 100)->nullable()->after('departement');

            // Index pour les recherches géographiques
            $table->index('region');
            $table->index(['region', 'departement']);
            $table->index(['region', 'departement', 'arrondissement']);
            $table->index(['ville_centre', 'region']);
        });
    }

    public function down()
    {
        Schema::table('centres', function (Blueprint $table) {
            $table->dropIndex(['ville_centre', 'region']);
            $table->dropIndex(['region', 'departement', 'arrondissement']);
            $table->dropIndex(['region', 'departement']);
            $table->dropIndex(['region']);
            $table->dropColumn(['arrondissement', 'departement', 'region']);
        });
    }
};
