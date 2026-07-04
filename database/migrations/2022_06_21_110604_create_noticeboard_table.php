<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoticeboardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('noticeboard', function (Blueprint $table) {
            $table->id();
            $table->longText('notice_title');
            $table->longText('notice');
            $table->string('start_date');
            $table->string('start_time');
            $table->string('end_date');
            $table->string('end_time');
            $table->integer('status');
            $table->integer('show_on_website');
            $table->string('image');
            $table->integer('school_id');
            $table->integer('session_id');
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
        Schema::dropIfExists('noticeboard');
    }
}
