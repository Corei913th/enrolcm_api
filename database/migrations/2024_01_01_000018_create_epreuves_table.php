<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TypeEpreuve;

return new class extends Migration
{
    public function up()
    {
        Schema::create('epreuves', function (Blueprint $table) {
            $table->uuid('id_epreuve')->primary();
            $table->string('intitule', 200);
            $table->string('session')->nullable();
            $table->text('url_epreuve')->nullable();
            $table->enum('type_epreuve', TypeEpreuve::values());
            $table->integer('duree_en_minute')->default(60);
            $table->boolean('est_actif')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index('session');
            $table->index('type_epreuve');
            $table->index('est_actif');
            $table->index(['session', 'type_epreuve']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('epreuves');
    }
};
