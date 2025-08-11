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
        // No changes needed as columns are already added in table creation
        return;
    }

    public function down()
    {
        // No changes needed
        return;
    }
};