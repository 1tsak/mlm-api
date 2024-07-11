<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mobile_number')->unique();
            $table->string('password');
            $table->string('sponsor_id')->unique();
            $table->string('referer_id')->nullable();
            $table->date('dob')->nullable();
            $table->string('address')->nullable();
            $table->decimal('balance', 15, 2)->default(0);
            $table->rememberToken();
            $table->timestamps();

            // Ensure an index on 'sponsor_id' to support foreign key constraints
            $table->index('sponsor_id');

            // Define foreign key constraint
            $table->foreign('referer_id')->references('sponsor_id')->on('users')->onDelete('set null');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
}
