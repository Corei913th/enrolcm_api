<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('concours', function (Blueprint $table) {
            $table->uuid('spec_concours_id')->nullable()->after('id');
            
            $table->foreign('spec_concours_id')->references('id')->on('specs_concours')->onDelete('set null');
            $table->index('spec_concours_id');
        });
    }

    public function down()
    {
        Schema::table('concours', function (Blueprint $table) {
            $table->dropForeign(['spec_concours_id']);
            $table->dropColumn('spec_concours_id');
        });
    }
};
