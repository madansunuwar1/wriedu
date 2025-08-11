<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('applications', function (Blueprint $table) {
            // Only add columns if they don't already exist
            if (!Schema::hasColumn('applications', 'status')) {
                $table->string('status')->default('active')->after('id');
            }

            if (!Schema::hasColumn('applications', 'canceled_at')) {
                $table->timestamp('canceled_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void {
        Schema::table('applications', function (Blueprint $table) {
            // Drop columns if they exist
            if (Schema::hasColumn('applications', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('applications', 'canceled_at')) {
                $table->dropColumn('canceled_at');
            }
        });
    }
};