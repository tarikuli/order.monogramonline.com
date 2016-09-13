<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailTemplatesTable extends Migration
{
	public function up ()
	{
		Schema::create('email_templates', function (Blueprint $table) {
			$table->increments('id');
			$table->string('message_type');
			$table->string('message_title');
			$table->text('message')->nullable();
			$table->enum('is_deleted', [0, 1]);
			$table->timestamps();
		});
		DB::update("ALTER TABLE email_templates AUTO_INCREMENT = 3;");
	}

	public function down ()
	{
		Schema::drop('email_templates');
	}
}
