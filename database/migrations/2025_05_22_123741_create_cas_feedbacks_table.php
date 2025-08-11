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
        Schema::create('cas_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('feedback_type')->nullable();
            $table->enum('priority', ['Low', 'Medium', 'High', 'Critical'])->nullable();
            $table->string('subject')->nullable();
            $table->text('feedback');
            $table->enum('status', ['Open', 'In Progress', 'Resolved', 'Closed'])->nullable();
            $table->string('entry_type')->default('cas_feedback');
            $table->timestamps();

            // Indexes for better performance
            $table->index(['application_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['status', 'priority']);
            $table->index('feedback_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cas_feedbacks');
    }
};