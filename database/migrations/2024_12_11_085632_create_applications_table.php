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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
    
            // Personal Information
            $table->string('name')->index();
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable()->index();
            $table->string('country')->nullable()->index();
            $table->string('location')->nullable();
    
            // Academic Information
            $table->string('lastqualification')->nullable();
            $table->string('passed')->nullable();
            $table->string('gpa')->nullable();
    
            // Product and English Proficiency
            $table->string('english')->nullable(); // Product field
            $table->string('englishTest')->nullable();
            $table->string('higher')->nullable(); // English test higher band
            $table->string('less')->nullable(); // English test lower band
            $table->string('score')->nullable(); // Calculated English test score
            $table->string('englishscore')->nullable(); // Overall English score
            $table->string('englishtheory')->nullable();
    
            // University and Course Details
            $table->string('university')->nullable();
            $table->string('course')->nullable();
            $table->string('intake')->nullable();
    
            // Application Processing
            $table->string('document')->nullable(); // Document status
            $table->string('additionalinfo')->nullable(); // Initiated offer information
    
            // Referral Information
            $table->string('source')->nullable()->index(); // Source of referral
            $table->string('partnerDetails')->nullable();
            $table->string('otherDetails')->nullable();
    
            // Search and Tracking Fields
            $table->string('searchField')->nullable();
            $table->string('customSearchField')->nullable();
            $table->string('courseSearchField')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->string('status')->default('active');
            $table->unsignedBigInteger('lead_id')->nullable(); // Or whatever data type is appropriate
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
    
            // Add the created_by column
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
    
            // Add index for better performance
            $table->index('user_id');
            // Additional Notes
            $table->text('notes')->nullable();
    
            // Timestamps
            $table->timestamps();
    
            // Optional: Soft delete if you want to keep records of deleted applications
            $table->softDeletes();
        });
    }
    
};
