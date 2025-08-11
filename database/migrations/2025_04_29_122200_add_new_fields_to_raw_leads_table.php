<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsToRawLeadsTable extends Migration
{
    public function up()
    {
        Schema::table('raw_leads', function (Blueprint $table) {
            $table->string('location')->nullable()->after('phone');
            $table->string('previous_degree')->nullable()->after('location');
            $table->string('twelfth_english_grade')->nullable()->after('previous_degree');
            $table->string('pass_degree_gpa')->nullable()->after('twelfth_english_grade');
            $table->year('pass_year')->nullable()->after('pass_degree_gpa');
            $table->string('english_proficiency')->nullable()->after('pass_year');
            $table->string('english_score')->nullable()->after('english_proficiency');
            $table->string('source')->nullable()->after('applying_for');
        });
    }

    public function down()
    {
        Schema::table('raw_leads', function (Blueprint $table) {
            $table->dropColumn([
                'location',
                'previous_degree',
                'twelfth_english_grade',
                'pass_degree_gpa',
                'pass_year',
                'english_proficiency',
                'english_score',
                'source',
            ]);
        });
    }
}
