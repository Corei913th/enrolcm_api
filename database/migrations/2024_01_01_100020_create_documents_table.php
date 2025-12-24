<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TypeDocument;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('candidature_id');
            $table->string('fichier_url', 500);
            $table->string('nom_original', 255);
            $table->enum('type_document', TypeDocument::values());
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('candidature_id')->references('id')->on('candidatures')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
