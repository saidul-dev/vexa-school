<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountHeadsTable extends Migration
{
    public function up()
    {
        Schema::create('account_heads', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id');
            $table->string('name');
            $table->string('type');
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->string('opening_balance_type')->default('debit');
            $table->boolean('is_system')->default(false);
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('account_heads');
    }
}
