<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up ()
	{
		Schema::create('settings', function (Blueprint $table) {
			$table->increments('id');
			$table->string('supervisor_station');
			$table->integer('default_shipping_rule')->default(0);
			$table->integer('default_route_id')->default(0);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 * @return void
	 */
	public function down ()
	{
		Schema::drop('settings');
	}
}
