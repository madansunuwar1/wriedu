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
        Schema::create('commission_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->string('student_name')->nullable();
            $table->string('university')->nullable();
            $table->string('intake')->nullable();
            $table->string('english')->nullable(); // 'english' is your product/course
            $table->enum('type', ['receivable', 'payable']);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('bonus_amount', 10, 2)->default(0);
            $table->decimal('incentive_amount', 10, 2)->default(0);
            $table->decimal('total_usd', 10, 2)->default(0);
            $table->decimal('exchange_rate', 12, 4)->default(1.0000);
            $table->decimal('total_npr', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->string('status')->default('pending'); // e.g., 'pending', 'received', 'paid', 'completed'
            $table->timestamp('received_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps(); // for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_history');
    }
};