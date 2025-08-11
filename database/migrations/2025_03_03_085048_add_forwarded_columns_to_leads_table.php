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
        Schema::table('leads', function (Blueprint $table) {
        $table->unsignedBigInteger('forwarded_to')->nullable(); // Foreign key for the user
        $table->text('forwarded_notes')->nullable(); // Notes regarding the forward
        $table->timestamp('forwarded_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('forwarded_to');
            $table->dropColumn('forwarded_notes');
            $table->dropColumn('forwarded_at');
        });
    }
};
