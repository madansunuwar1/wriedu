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
        // Drop existing table if it exists
        Schema::dropIfExists('notifications');
        
        // Create new notifications table with our custom structure
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Auto-incrementing ID
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('general'); // Notification type (comment, mention, notice, etc.)
            $table->string('message'); // The notification message
            $table->text('content')->nullable(); // Optional content details
            $table->string('link')->nullable(); // Optional link to relevant content
            $table->boolean('read')->default(false); // Read status
            $table->json('data')->nullable(); // Additional data in JSON format
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};