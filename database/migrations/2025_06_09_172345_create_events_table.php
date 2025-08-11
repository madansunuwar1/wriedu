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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('color')->default('primary');
            $table->unsignedBigInteger('notice_id')->nullable();
            $table->string('type')->default('event');
            $table->timestamps();
            
            // Add foreign key constraint
            $table->foreign('notice_id')->references('id')->on('notices')->onDelete('cascade');
            
            // Add indexes for better performance
            $table->index(['start_date', 'end_date']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};