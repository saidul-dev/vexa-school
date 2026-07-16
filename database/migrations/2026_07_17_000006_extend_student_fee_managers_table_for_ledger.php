<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ExtendStudentFeeManagersTableForLedger extends Migration
{
    public function up()
    {
        DB::table('student_fee_managers')->delete();

        Schema::table('student_fee_managers', function (Blueprint $table) {
            $table->unsignedBigInteger('account_head_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('student_fee_managers', function (Blueprint $table) {
            $table->dropColumn('account_head_id');
        });
    }
}
