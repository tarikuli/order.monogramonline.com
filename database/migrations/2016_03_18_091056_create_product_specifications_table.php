<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductSpecificationsTable extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up ()
	{
		Schema::create('product_specifications', function (Blueprint $table) {
			$table->increments('id');
			$table->string('id_catalog');
			$table->string('product_model');
			$table->integer('product_id');
			$table->text('custom_data');

			$table->index('id_catalog');
			$table->index('product_model');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 * @return void
	 */
	public function down ()
	{
		Schema::drop('product_specifications');
	}
}
