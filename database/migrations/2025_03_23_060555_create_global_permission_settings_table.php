<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGlobalPermissionSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('global_permission_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->boolean('value')->default(false);
            $table->text('description')->nullable();
            $table->foreignId('affects_permission_id')->nullable()->constrained('permissions')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('global_permission_settings');
    }
}