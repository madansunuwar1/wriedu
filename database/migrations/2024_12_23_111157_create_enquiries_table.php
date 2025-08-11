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
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('uname');
            $table->string('email');
            $table->string('contact');
            $table->string('guardians');
            $table->string('contacts');
            $table->string('country');
            $table->string('education');
            $table->string('about');
            $table->string('ielts');
            $table->string('toefl');
            $table->string('ellt');
            $table->string('pte');
            $table->string('sat');
            $table->string('gre');
            $table->string('gmat');
            $table->string('other');
            $table->string('feedback');
            $table->string('counselor');
            $table->string('institution1');
            $table->string('grade1');
            $table->string('year1');
            $table->string('institution2');
            $table->string('grade2');
            $table->string('year2');
            $table->string('institution3');
            $table->string('grade3');
            $table->string('year3');
            $table->string('institution4');
            $table->string('grade4');
            $table->string('year4');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
