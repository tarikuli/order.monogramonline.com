<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductOccasionTable extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up ()
	{
		Schema::create('occasion_product', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('product_id');
			$table->integer('occasion_id');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 * @return void
	 */
	public function down ()
	{
		Schema::drop('product_occasion');
	}
}
