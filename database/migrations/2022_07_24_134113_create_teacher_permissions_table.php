<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_permissions', function (Blueprint $table) {
            $table->id();
            $table->integer('class_id');
            $table->integer('section_id');
            $table->integer('school_id');
            $table->integer('teacher_id');
            $table->integer('marks');
            $table->integer('attendance');
            $table->integer('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teacher_permissions');
    }
};
