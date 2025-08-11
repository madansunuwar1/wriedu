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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('locations')->nullable();
            $table->string('lastqualification')->nullable();
            $table->string('courselevel')->nullable();
            $table->string('passed')->nullable();
            $table->string('gpa')->nullable();
            $table->string('englishTest')->nullable();
            $table->string('academic')->nullable(); // Make this column nullable
            $table->string('higher')->nullable();
            $table->string('less')->nullable();
            $table->string('score')->nullable();
            $table->string('englishscore')->nullable();
            $table->string('englishtheory')->nullable();
            $table->string('otherScore')->nullable();
            $table->string('country')->nullable();
            $table->string('location')->nullable();
            $table->string('university')->nullable();
            $table->string('course')->nullable();
            $table->string('intake')->nullable();
            $table->string('offerss')->nullable();
            $table->string('source')->nullable();
            $table->string('otherDetails')->nullable();
            $table->string('sources')->nullable();
            $table->string('link')->nullable();
            $table->boolean('is_forwarded')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
