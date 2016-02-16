<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotesTable extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up ()
	{
		Schema::create('notes', function (Blueprint $table) {
			$table->increments('id');
			$table->string('order_id');
			$table->integer('user_id');
			$table->text('note_text');
			$table->enum('is_deleted', [0, 1])->default(0);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 * @return void
	 */
	public function down ()
	{
		Schema::drop('notes');
	}
}
