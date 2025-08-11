<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('commission_transactions', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys - Fixed spelling
            $table->unsignedBigInteger('commission_id')->nullable(); // Changed from 'comission_id'
            $table->unsignedBigInteger('application_id')->nullable();
            $table->unsignedBigInteger('commissionpayable_id')->nullable(); // if it's optional
            $table->unsignedBigInteger('user_id'); // agent/partner/etc.

            
            $table->enum('type', ['payable', 'receivable']);
            $table->decimal('amount')->nullable();
            $table->decimal('exchange_rate')->nullable();
            $table->string('paid')->nullable();
            $table->string('status')->default('pending');
            $table->date('due_date')->nullable();
            $table->timestamps();
            
            // Foreign key constraints - Fixed spelling
            $table->foreign('commission_id')->references('id')->on('comissions')->onDelete('cascade'); // Also fix table name
            $table->foreign('commissionpayable_id')->references('id')->on('commission_payable')->onDelete('cascade'); // Also fix table name
            $table->foreign('application_id')->references('id')->on('applications')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('commission_transactions');
    }
}