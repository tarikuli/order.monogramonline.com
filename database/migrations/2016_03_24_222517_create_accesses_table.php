<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccessesTable extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up ()
	{
		Schema::create('accesses', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id');
			$table->string('page', 50);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 * @return void
	 */
	public function down ()
	{
		Schema::drop('accesses');
	}
}
