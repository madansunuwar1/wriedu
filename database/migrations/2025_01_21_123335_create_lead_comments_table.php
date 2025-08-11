<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('lead_comments', function (Blueprint $table) {
        $table->id();
        $table->text('comment');
        $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
        $table->string('comment_type')->default('general');
        $table->unsignedBigInteger('lead_id')->nullable();
        $table->unsignedBigInteger('application_id')->nullable();
        $table->unsignedBigInteger('enquiry_id')->nullable();
        $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
        $table->string('editor_name')->nullable();
        $table->timestamps();
    });
    
    // Be very explicit with the foreign key definition
    Schema::table('lead_comments', function (Blueprint $table) {
        $table->foreign('application_id')
              ->references('id')  // Make sure this column exists in applications table
              ->on('applications')
              ->onDelete('cascade');
              
        // Define the other foreign keys similarly
        $table->foreign('lead_id')
              ->references('id')
              ->on('leads')
              ->onDelete('cascade');
              
        $table->foreign('enquiry_id')
              ->references('id')
              ->on('enquiries')
              ->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_comments');
    }
};
