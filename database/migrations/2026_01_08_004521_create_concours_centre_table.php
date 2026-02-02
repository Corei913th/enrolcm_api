<?php

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
        Schema::create('concours_centre', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('concours_id')->constrained('concours')->onDelete('cascade');
            $table->foreignUuid('centre_id')->constrained('centres')->onDelete('cascade');
            $table->boolean('est_actif')->default(true);
            $table->timestamps();

            $table->unique(['concours_id', 'centre_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concours_centre');
    }
};
