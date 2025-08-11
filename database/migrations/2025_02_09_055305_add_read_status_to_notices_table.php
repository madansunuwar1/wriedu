<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('notices', function (Blueprint $table) {
            // Add the read_status column if it doesn't exist
            if (!Schema::hasColumn('notices', 'read_status')) {
                $table->boolean('read_status')->default(false);
            }
        });
    }

    public function down()
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->dropColumn('read_status');
        });
    }
};