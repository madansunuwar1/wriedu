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
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('wantToStudy');
            $table->string('location');
            $table->string('courselevel');
            $table->string('lastqualifications');
            $table->string('passed');
            $table->string('englishTest');
            $table->string('sources')->nullable();
            $table->unsignedBigInteger('forwarded_to')->nullable();
            $table->text('forwarded_notes')->nullable();
            $table->boolean('is_forwarded')->default(false);
            $table->timestamp('forwarded_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');


            // Add foreign key constraint if users table exists
            $table->foreign('forwarded_to')->references('id')->on('users')->onDelete('set null');
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculties');
    }
};
