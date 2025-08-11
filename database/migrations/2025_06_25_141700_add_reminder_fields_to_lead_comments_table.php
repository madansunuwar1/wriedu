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
        Schema::table('lead_comments', function (Blueprint $table) {
            // Add a nullable dateTime column to store the reminder time.
            // It's nullable because not every comment will be a reminder.
            $table->dateTime('date_time')->nullable()->after('comment');

            // Add a boolean column to track if the reminder is completed.
            // We default it to `false`.
            $table->boolean('is_completed')->default(false)->after('date_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead_comments', function (Blueprint $table) {
            // This allows you to undo the migration if needed.
            $table->dropColumn(['date_time', 'is_completed']);
        });
    }
};