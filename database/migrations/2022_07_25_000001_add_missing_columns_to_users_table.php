<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->longText('student_info')->nullable();
            $table->longText('documents')->nullable();
            $table->string('status')->nullable();
            $table->integer('department_id')->nullable();
            $table->string('designation')->nullable();
            $table->string('language')->nullable();
            $table->integer('school_role')->nullable();
            $table->string('account_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['student_info', 'documents', 'status', 'department_id', 'designation', 'language', 'school_role', 'account_status']);
        });
    }
}
