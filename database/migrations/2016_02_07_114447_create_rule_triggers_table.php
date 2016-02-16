<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRuleTriggersTable extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up ()
	{
		Schema::create('rule_triggers', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('rule_id');
			$table->string('rule_trigger_parameter');
			$table->string('rule_trigger_relation');
			$table->text('rule_trigger_value');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 * @return void
	 */
	public function down ()
	{
		Schema::drop('rule_triggers');
	}
}
