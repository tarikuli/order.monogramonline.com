<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductSpecificationSheet extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up ()
	{
		Schema::create('product_specification_sheet', function (Blueprint $table) {
			$table->increments('id');
			$table->string('product_name');
			$table->string('product_sku');
			$table->text('product_description');
			$table->double('product_weight');
			$table->double('product_length');
			$table->double('product_width');
			$table->double('product_height');
			$table->string('packaging_type_name');
			$table->string('packaging_size');
			$table->double('packaging_weight');
			$table->double('total_weight');
			$table->integer('production_category');
			$table->string('art_work_location');
			$table->string('temperature');
			$table->string('dwell_time');
			$table->string('pressure');
			$table->string('run_time');
			$table->text('type');
			$table->string('font');
			$table->string('variation_name');

			$table->text('special_note');

			$table->text('product_note');

			$table->double('cost_of_1');
			$table->double('cost_of_10');
			$table->double('cost_of_100');
			$table->double('cost_of_1000');
			$table->double('cost_of_10000');

			$table->text('content_cost_info');
			$table->text('delivery_cost_variation');
			$table->text('labor_expense_cost_variation');

			$table->text('images');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 * @return void
	 */
	public function down ()
	{
		Schema::drop('product_specification_sheet');
	}
}
