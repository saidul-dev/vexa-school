<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountVouchersTable extends Migration
{
    public function up()
    {
        Schema::create('account_vouchers', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id');
            $table->integer('session_id');
            $table->string('voucher_no');
            $table->string('voucher_type');
            $table->date('voucher_date');
            $table->string('particulars');
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('account_vouchers');
    }
}
