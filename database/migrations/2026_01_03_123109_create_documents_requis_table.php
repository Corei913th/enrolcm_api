<?php

use App\Enums\TypeDocument;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents_requis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('concours_id');
            $table->foreign('concours_id')->references('id')->on('concours')->onDelete('cascade');
            $table->string('nom_document');
            $table->text('description')->nullable();
            $table->enum('type_document', TypeDocument::values());
            $table->boolean('est_obligatoire')->default(true);
            $table->json('format_accepte')->nullable(); // ['pdf', 'jpg', 'jpeg', 'png']
            $table->integer('taille_max_mb')->default(5);
            $table->boolean('est_actif')->default(true);
            $table->integer('ordre_affichage')->default(0);
            $table->timestamps();



            $table->index(['concours_id', 'est_actif']);
            $table->index('ordre_affichage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents_requis');
    }
};
