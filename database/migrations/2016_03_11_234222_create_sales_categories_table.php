<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_categories', function (Blueprint $table) {
            $table->increments('id');
			$table->string('sales_category_code');
			$table->string('sales_category_description');
			$table->string('sales_category_display_order');
			$table->enum('is_deleted', array(0, 1))->default(0);
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
        Schema::drop('sales_categories');
    }
}
