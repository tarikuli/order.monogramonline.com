<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParameterOptionsTable extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up ()
	{
		Schema::create('parameter_options', function (Blueprint $table) {
			$table->increments('id');
			$table->string('store_id');
			$table->integer('parameter_id');
			$table->text('parameter_option')
				  ->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 * @return void
	 */
	public function down ()
	{
		Schema::drop('parameter_options');
	}
}
