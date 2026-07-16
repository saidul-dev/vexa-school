<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ExtendExpensesTableForLedger extends Migration
{
    public function up()
    {
        DB::table('expenses')->delete();

        Schema::table('expenses', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->unsignedBigInteger('account_head_id')->nullable();
            $table->unsignedBigInteger('payment_account_head_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['title', 'account_head_id', 'payment_account_head_id']);
        });
    }
}
