<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedByToLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->index(); // Add column with index
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null'); // Foreign key
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['created_by']); // Drop foreign key
            $table->dropIndex(['created_by']);   // Drop index
            $table->dropColumn('created_by');    // Drop column
        });
    }
}

