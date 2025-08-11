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
        Schema::table('uploads', function (Blueprint $table) {
            // Check if columns exist before adding them
            if (!Schema::hasColumn('uploads', 'filename')) {
                $table->string('filename')->after('lead_id');
            }
            
            if (!Schema::hasColumn('uploads', 'original_name')) {
                $table->string('original_name')->after('filename');
            }
            
            if (!Schema::hasColumn('uploads', 'file_path')) {
                $table->string('file_path')->after('original_name');
            }
            
            if (!Schema::hasColumn('uploads', 'file_size')) {
                $table->unsignedBigInteger('file_size')->nullable()->after('file_path');
            }
            
            if (!Schema::hasColumn('uploads', 'mime_type')) {
                $table->string('mime_type')->nullable()->after('file_size');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uploads', function (Blueprint $table) {
            $table->dropColumn(['filename', 'original_name', 'file_path', 'file_size', 'mime_type']);
        });
    }
};