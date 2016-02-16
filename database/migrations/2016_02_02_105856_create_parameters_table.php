<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParametersTable extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up ()
	{
		Schema::create('parameters', function (Blueprint $table) {
			$table->increments('id');
			$table->string('store_id');
			$table->string('parameter_value')->nullable();
			$table->enum('is_deleted', array(0, 1))->default(0);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 * @return void
	 */
	public function down ()
	{
		Schema::drop('parameters');
	}
}
