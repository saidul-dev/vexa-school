<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeExpenseCategoryIdNullable extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE expenses MODIFY expense_category_id INT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE expenses MODIFY expense_category_id INT NOT NULL');
    }
}
