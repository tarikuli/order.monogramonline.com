<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateVendorsTable extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up ()
	{
		Schema::table('vendors', function (Blueprint $table) {
			$table->string('image')
				  ->nullable();
			$table->string('contact_person_name')
				  ->nullable();
			$table->string("link")
				  ->nullable();
			$table->string('login_id')
				  ->nullable();
			$table->string('password')
				  ->nullable();
			$table->string('bank_info')
				  ->nullable();
			$table->string('paypal_info')
				  ->nullable();
			$table->text('notes')
				  ->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 * @return void
	 */
	public function down ()
	{
		Schema::table('vendors', function (Blueprint $table) {
			$table->dropColumn([
				'notes',
				'paypal_info',
				'bank_info',
				'password',
				'login_id',
				'link',
				'contact_person_name',
			]);
		});
	}
}
