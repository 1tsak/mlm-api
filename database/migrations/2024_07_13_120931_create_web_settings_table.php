<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('web_settings', function (Blueprint $table) {
            $table->id();
            $table->string('telegram_share_link');
            $table->string('telegram_chat_link');
            $table->decimal('refer_bonus', 8, 2);
            $table->decimal('level_percentage', 5, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('web_settings');
    }
};
