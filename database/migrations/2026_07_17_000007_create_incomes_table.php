<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomesTable extends Migration
{
    public function up()
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('account_head_id');
            $table->unsignedBigInteger('payment_account_head_id');
            $table->integer('date');
            $table->decimal('amount', 12, 2);
            $table->integer('school_id');
            $table->integer('session_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('incomes');
    }
}
