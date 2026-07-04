<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMissingFeatureTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_history', function (Blueprint $table) {
            $table->id();
            $table->string('payment_type')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('course_id')->nullable();
            $table->string('amount')->nullable();
            $table->integer('school_id')->nullable();
            $table->longText('transaction_keys')->nullable();
            $table->string('document_image')->nullable();
            $table->integer('paid_by')->nullable();
            $table->string('status')->nullable();
            $table->integer('timestamp')->nullable();
            $table->timestamps();
        });

        Schema::create('message_thrades', function (Blueprint $table) {
            $table->id();
            $table->integer('reciver_id')->nullable();
            $table->integer('sender_id')->nullable();
            $table->integer('school_id')->nullable();
            $table->timestamps();
        });

        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->integer('message_thrade')->nullable();
            $table->integer('reciver_id')->nullable();
            $table->integer('sender_id')->nullable();
            $table->longText('message')->nullable();
            $table->integer('reply_id')->nullable();
            $table->integer('school_id')->nullable();
            $table->integer('read_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chats');
        Schema::dropIfExists('message_thrades');
        Schema::dropIfExists('payment_history');
    }
}
