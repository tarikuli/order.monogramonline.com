<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCollectionTable extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up ()
	{
		Schema::create('collection_product', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('product_id');
			$table->integer('collection_id');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 * @return void
	 */
	public function down ()
	{
		Schema::drop('product_collection');
	}
}
