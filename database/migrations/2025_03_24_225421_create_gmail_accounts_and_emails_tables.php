<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGmailAccountsAndEmailsTables extends Migration
{
    public function up()
    {
        Schema::create('gmail_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('email')->unique();
            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });

        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gmail_account_id');
            $table->string('message_id')->unique();
            $table->string('subject')->nullable();
            $table->string('sender');
            $table->string('recipient');
            $table->text('body')->nullable();
            $table->text('raw_content')->nullable();
            $table->timestamp('received_at');
            $table->boolean('is_read')->default(false);
            $table->json('labels')->nullable();
            $table->timestamps();

            $table->foreign('gmail_account_id')
                  ->references('id')
                  ->on('gmail_accounts')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('emails');
        Schema::dropIfExists('gmail_accounts');
    }
}