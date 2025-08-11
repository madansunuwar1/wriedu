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
    Schema::create('data_entries', function (Blueprint $table) {
        $table->id();
        $table->string('newUniversity');
        $table->string('newLocation');
        $table->string('newCourse');
        $table->string('newIntake');
        $table->string('newScholarship');
        $table->string('newAmount');
        $table->string('newIelts');
        $table->string('newpte');
        $table->string('newPgIelts');
        $table->string('newPgPte');
        $table->string('newug');
        $table->string('newpg');
        $table->string('newgpaug');
        $table->string('newgpapg');
        $table->string('newtest');
        $table->string('country');
        $table->string('requireddocuments');
        $table->string('level');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_entries');
    }
};
