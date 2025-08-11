<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up()
    {
        Schema::create('comment_mentions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained('lead_comments')->onDelete('cascade');
            $table->foreignId('mentioned_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mentioner_id')->constrained('users')->onDelete('cascade');
            $table->string('user_name');  // Store username directly
            $table->timestamps();
            
            // Updated unique constraint
            $table->unique(['comment_id', 'mentioned_user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('comment_mentions');
    }
};