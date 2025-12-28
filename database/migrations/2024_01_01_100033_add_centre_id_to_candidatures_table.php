<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('candidatures', function (Blueprint $table) {
            $table->uuid('centre_id')->nullable()->after('session_id');
            
            $table->foreign('centre_id')->references('id')->on('centres')->onDelete('set null');
            $table->index('centre_id');
        });
    }

    public function down()
    {
        Schema::table('candidatures', function (Blueprint $table) {
            $table->dropForeign(['centre_id']);
            $table->dropColumn('centre_id');
        });
    }
};
