<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('notices', function (Blueprint $table) {
        if (!Schema::hasColumn('notices', 'read_status')) {
            $table->boolean('read_status')->default(false);
            $table->timestamp('read_at')->nullable()->after('read_status');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->dropColumn('read_status');
            $table->dropColumn('read_at');
        });
    }
};
