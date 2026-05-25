<?php

use App\Enums\CanalNotification;
use App\Enums\PrioriteNotification;
use App\Enums\TypeNotification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('utilisateur_id');
            $table->enum('type_notification', TypeNotification::values());
            $table->string('titre', 200);
            $table->text('message');
            $table->enum('canal', CanalNotification::values())->default(CanalNotification::APP);
            $table->boolean('est_lue')->default(false);
            $table->timestamp('date_lecture')->nullable();
            $table->boolean('est_envoyee')->default(false);
            $table->timestamp('date_envoi')->nullable();
            $table->json('metadata')->nullable();
            $table->enum('priorite', PrioriteNotification::values())->default(PrioriteNotification::NORMALE);
            $table->timestamps();

            $table->foreign('utilisateur_id')->references('id')->on('utilisateurs')->onDelete('cascade');

            $table->index('utilisateur_id');
            $table->index('type_notification');
            $table->index('est_lue');
            $table->index('est_envoyee');
            $table->index('canal');
            $table->index('priorite');
            $table->index(['utilisateur_id', 'est_lue']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
