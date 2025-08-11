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
      Schema::create('security_events', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id');
    $table->string('session_id');
    $table->string('event_type');
    $table->json('details');
    $table->ipAddress('ip_address');
    $table->text('user_agent');
    $table->timestamp('timestamp');
    $table->timestamps();

    $table->foreign('user_id')->references('id')->on('users');
    $table->index(['user_id', 'event_type']);
    $table->index('timestamp');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_events');
    }
};
