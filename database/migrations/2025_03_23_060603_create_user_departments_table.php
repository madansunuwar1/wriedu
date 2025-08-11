<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDepartmentsTable extends Migration
{
    public function up()
    {
        Schema::create('user_departments', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->primary(['user_id', 'department_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_departments');
    }
}