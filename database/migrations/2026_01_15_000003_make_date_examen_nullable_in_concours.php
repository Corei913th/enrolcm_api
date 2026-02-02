<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   * 
   * Rendre date_examen nullable car la vraie source de vérité
   * est planning_epreuves.date_epreuve
   */
  public function up(): void
  {
    Schema::table('concours', function (Blueprint $table) {
      $table->date('date_examen')->nullable()->change();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('concours', function (Blueprint $table) {
      $table->date('date_examen')->nullable(false)->change();
    });
  }
};
