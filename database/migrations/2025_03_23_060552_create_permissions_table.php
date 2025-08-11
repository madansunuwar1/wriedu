<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
          
            $table->foreignId('category_id')->nullable()->constrained('permission_categories')->onDelete('set null');
            $table->string('code', 100)->unique();
            $table->string('category')->nullable()->default('default_category');
            $table->timestamps();
        });
    }
    

    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}