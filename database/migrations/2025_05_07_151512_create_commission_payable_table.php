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
        Schema::create('commission_payable', function (Blueprint $table) {
            $table->id();

            $table->string('university');
            $table->string('product');
            $table->string('partner');
            $table->boolean('has_progressive_commission')->default(false);
            $table->string('progressive_commission')->nullable();
            $table->boolean('has_bonus_commission')->default(false);
            $table->string('bonus_commission')->nullable();
            $table->boolean('has_incentive_commission')->default(false);
            $table->string('incentive_commission')->nullable();
            $table->json('commission_types')->nullable();

            $table->string('paid')->nullable();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_payable');
    }
};
