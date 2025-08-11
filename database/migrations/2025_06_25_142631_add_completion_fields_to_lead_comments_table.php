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
            // Add a column to store who completed the reminder.
            // It should be nullable and can be a foreign key to your users table.
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null')->after('is_completed');

            // Add a column to store when the reminder was completed.
            $table->timestamp('completed_at')->nullable()->after('completed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead_comments', function (Blueprint $table) {
            // This allows you to roll back the migration.
            $table->dropForeign(['completed_by']);
            $table->dropColumn(['completed_by', 'completed_at']);
        });
    }
};